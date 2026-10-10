<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PlotRentalOffer;
use App\Services\PlotRentalException;
use App\Services\PlotRentalService;
use App\Services\Plugin\PlotService;
use Illuminate\Http\Request;

/** Offering a plot for rent at the makelaar, and renting one. */
class PlotRentalOfferController extends Controller
{
    public function __construct(protected PlotRentalService $rentals, protected PlotService $plotService)
    {
    }

    public function create(string $plotName)
    {
        $plot = collect(auth()->user()->plots)->firstWhere('name', $plotName);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return redirect()->route('portal.plots.show', $plotName)
                ->with('error', ['title' => 'Actie niet mogelijk', 'message' => 'Je kunt alleen je eigen plots verhuren.']);
        }

        if ($error = self::unavailable($plotName)) {
            return redirect()->route('portal.plots.show', $plotName)->with('error', $error);
        }

        return view('portal.plots.rentals.create', ['plot' => $plot]);
    }

    public function store(Request $request, string $plotName)
    {
        $validated = self::validateOffer($request, payoutRequired: true);

        $plot = collect(auth()->user()->plots)->firstWhere('name', $plotName);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return redirect()->route('portal.plots.show', $plotName)
                ->with('error', ['title' => 'Actie niet mogelijk', 'message' => 'Je kunt alleen je eigen plots verhuren.']);
        }

        if (!collect(auth()->user()->bank_accounts)->contains('uuid', $validated['payout_bank_account_uuid'])) {
            return back()->withInput()->withErrors(['payout_bank_account_uuid' => 'Kies een van je eigen bankrekeningen.']);
        }

        if ($error = self::unavailable($plotName)) {
            return redirect()->route('portal.plots.show', $plotName)->with('error', $error);
        }

        self::createOffer($request, $validated, $plot, isServerListing: false);

        return redirect()->route('portal.plots.show', $plotName)
            ->with('success', [
                'title' => 'Plot te huur gezet!',
                'message' => 'Je plot staat nu te huur bij de makelaar. Een huurder wordt lid van het plot; jij blijft eigenaar.',
            ]);
    }

    public function showRentForm(PlotRentalOffer $offer)
    {
        if ($error = $this->cannotRent($offer)) {
            return redirect()->route('portal.plots.listings.index', ['soort' => 'huur'])->with('error', $error);
        }

        return view('portal.plots.rentals.rent', ['offer' => $offer]);
    }

    public function rent(Request $request, PlotRentalOffer $offer)
    {
        $validated = $request->validate([
            'renter_bank_account_uuid' => 'required|string',
            'terms' => 'required|accepted',
        ], [
            'renter_bank_account_uuid.required' => 'Selecteer een bankrekening om mee te betalen.',
            'terms.accepted' => 'Je moet akkoord gaan met de huurvoorwaarden.',
        ]);

        if ($error = $this->cannotRent($offer)) {
            return back()->with('error', $error);
        }

        try {
            $this->rentals->start($offer, auth()->user(), $validated['renter_bank_account_uuid']);
        } catch (PlotRentalException $e) {
            return back()->with('error', $e->flash());
        }

        return redirect()->route('portal.plots.rentals.index')
            ->with('success', [
                'title' => 'Plot gehuurd!',
                'message' => "Je huurt nu {$offer->plot_name}. De huur wordt elke ".($offer->period === 'week' ? 'week' : 'maand').' automatisch verlengd.',
            ]);
    }

    public function destroy(PlotRentalOffer $offer)
    {
        $user = auth()->user();

        $isLandlord = $offer->landlord_id === $user->id && $user->can('use-broker');
        $isManager = $user->can('manage-broker');

        if (!$isLandlord && !$isManager) {
            return back()->with('error', ['title' => 'Actie niet mogelijk', 'message' => 'Je kunt alleen je eigen plots van de markt halen.']);
        }

        if ($offer->status === 'rented') {
            return back()->with('error', ['title' => 'Plot wordt verhuurd', 'message' => 'Zeg eerst de huur op. Daarna kun je het plot van de markt halen.']);
        }

        $offer->update(['status' => 'cancelled']);

        if ($isLandlord && !$offer->is_server_listing) {
            $redirect = redirect()->route('portal.plots.show', $offer->plot_name);
        } elseif ($user->can('manage-plots')) {
            $redirect = redirect()->route('portal.admin.plots.show', $offer->plot_name);
        } else {
            $redirect = back();
        }

        return $redirect->with('success', [
            'title' => 'Plot van de markt gehaald',
            'message' => 'Het plot staat niet meer te huur.',
        ]);
    }

    protected function cannotRent(PlotRentalOffer $offer): ?array
    {
        if ($offer->landlord_id === auth()->id() && !$offer->is_server_listing) {
            return ['title' => 'Actie niet mogelijk', 'message' => 'Je kunt je eigen plot niet huren.'];
        }

        if ($offer->status !== 'active') {
            return ['title' => 'Plot niet beschikbaar', 'message' => 'Dit plot is niet meer te huur.'];
        }

        return null;
    }

    /** Why a plot cannot be offered for rent now, or null. Also used for server plots. */
    public static function unavailable(string $plotName): ?array
    {
        if (PlotRentalOffer::where('plot_name', $plotName)->whereIn('status', ['active', 'rented'])->exists()) {
            return ['title' => 'Actie niet mogelijk', 'message' => 'Dit plot staat al te huur of wordt verhuurd.'];
        }

        return null;
    }

    public static function validateOffer(Request $request, bool $payoutRequired): array
    {
        return $request->validate([
            'price' => 'required|numeric|min:0.01|max:99999999',
            'period' => 'required|in:week,month',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'payout_bank_account_uuid' => ($payoutRequired ? 'required' : 'nullable').'|string',
        ], [
            'period.in' => 'Kies per week of per maand.',
            'payout_bank_account_uuid.required' => 'Kies de bankrekening waarop je de huur wilt ontvangen.',
        ]);
    }

    public static function createOffer(Request $request, array $validated, array $plot, bool $isServerListing, ?string $imagePath = null): PlotRentalOffer
    {
        return PlotRentalOffer::create([
            'plot_name' => $plot['name'],
            'landlord_id' => auth()->id(),
            'is_server_listing' => $isServerListing,
            'payout_bank_account_uuid' => $validated['payout_bank_account_uuid'] ?? null,
            'price' => $validated['price'],
            'period' => $validated['period'],
            'description' => $validated['description'],
            'image_path' => $imagePath ?? ($request->hasFile('image') ? $request->file('image')->store('plot-listings', 'public') : null),
            'min_x' => $plot['location']['min']['x'] ?? 0,
            'min_y' => $plot['location']['min']['y'] ?? 0,
            'min_z' => $plot['location']['min']['z'] ?? 0,
            'max_x' => $plot['location']['max']['x'] ?? 0,
            'max_y' => $plot['location']['max']['y'] ?? 0,
            'max_z' => $plot['location']['max']['z'] ?? 0,
            'status' => 'active',
        ]);
    }
}
