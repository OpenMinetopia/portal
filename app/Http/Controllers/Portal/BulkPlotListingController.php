<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PlotListing;
use App\Services\PlotRentalService;
use App\Services\Plugin\PlotService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Support\Portal;

/**
 * Several plots to the makelaar at once, for sale or for rent, with one set of
 * settings: one listing or rental offer per plot. Players list their own OWNER
 * plots from Mijn plots; admins list any plot for the server from Admin → Plots.
 * Plots that are already for sale, for rent or rented are skipped and named.
 */
class BulkPlotListingController extends Controller
{
    public function __construct(protected PlotService $plotService)
    {
    }

    public function create(Request $request)
    {
        $plots = $this->ownPlots()->only((array) $request->query('plots', []));

        if ($plots->isEmpty()) {
            return redirect()->route('portal.plots.index')
                ->with('error', ['title' => 'Geen plots geselecteerd', 'message' => 'Selecteer een of meer plots waarvan je eigenaar bent.']);
        }

        return view('portal.plots.bulk.create', [
            'plots' => $plots->values(),
            'isServerListing' => false,
            'storeUrl' => route('portal.plots.bulk.store'),
            'backUrl' => route('portal.plots.index'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBulk($request, payoutRequired: true);

        if (!collect(auth()->user()->bank_accounts)->contains('uuid', $validated['payout_bank_account_uuid'])) {
            return back()->withInput()->withErrors(['payout_bank_account_uuid' => 'Kies een van je eigen bankrekeningen.']);
        }

        $owned = $this->ownPlots();
        $notOwned = collect($validated['plots'])->reject(fn ($name) => $owned->has($name));

        if ($notOwned->isNotEmpty()) {
            return back()->withInput()->withErrors(['plots' => 'Je bent geen eigenaar van: '.$notOwned->implode(', ').'. Er is niets op de markt gezet.']);
        }

        return $this->list($request, $validated, $owned->only($validated['plots']), isServerListing: false, redirect: route('portal.plots.index'));
    }

    public function adminCreate(Request $request)
    {
        $plots = $this->allPlots()->only((array) $request->query('plots', []));

        if ($plots->isEmpty()) {
            return redirect()->route('portal.admin.plots.index')
                ->with('error', ['title' => 'Geen plots geselecteerd', 'message' => 'Selecteer een of meer plots.']);
        }

        return view('portal.plots.bulk.create', [
            'plots' => $plots->values(),
            'isServerListing' => true,
            'storeUrl' => route('portal.admin.plots.bulk.store'),
            'backUrl' => route('portal.admin.plots.index'),
        ]);
    }

    public function adminStore(Request $request)
    {
        $validated = $this->validateBulk($request, payoutRequired: false);

        $all = $this->allPlots();
        $unknown = collect($validated['plots'])->reject(fn ($name) => $all->has($name));

        if ($unknown->isNotEmpty()) {
            return back()->withInput()->withErrors(['plots' => 'Onbekende plots: '.$unknown->implode(', ').'. Er is niets op de markt gezet.']);
        }

        return $this->list($request, $validated, $all->only($validated['plots']), isServerListing: true, redirect: route('portal.admin.plots.index'));
    }

    /** Creates the listings or offers in one transaction and reports what was skipped. */
    protected function list(Request $request, array $validated, Collection $plots, bool $isServerListing, string $redirect)
    {
        $rent = $validated['type'] === 'rent';
        $imagePath = $request->hasFile('image') ? $request->file('image')->store('plot-listings', 'public') : null;

        [$created, $skipped] = DB::transaction(function () use ($request, $validated, $plots, $isServerListing, $rent, $imagePath) {
            $created = [];
            $skipped = [];

            foreach ($plots as $name => $plot) {
                if ($reason = $this->skipReason($plot, $rent)) {
                    $skipped[$name] = $reason;
                    continue;
                }

                if ($rent) {
                    PlotRentalOfferController::createOffer($request, $validated, $plot, $isServerListing, $imagePath);
                } else {
                    PlotListing::create([
                        'plot_name' => $name,
                        'seller_id' => auth()->id(),
                        'is_server_listing' => $isServerListing,
                        'payout_bank_account_uuid' => $validated['payout_bank_account_uuid'] ?? null,
                        'price' => $validated['price'],
                        'description' => $validated['description'],
                        'image_path' => $imagePath,
                        'min_x' => $plot['location']['min']['x'] ?? 0,
                        'min_y' => $plot['location']['min']['y'] ?? 0,
                        'min_z' => $plot['location']['min']['z'] ?? 0,
                        'max_x' => $plot['location']['max']['x'] ?? 0,
                        'max_y' => $plot['location']['max']['y'] ?? 0,
                        'max_z' => $plot['location']['max']['z'] ?? 0,
                        'instant_buy' => $request->boolean('instant_buy'),
                    ]);
                }

                $created[] = $name;
            }

            return [$created, $skipped];
        });

        if ($imagePath && $created === []) {
            Storage::disk('public')->delete($imagePath);
        }

        $skippedText = collect($skipped)->map(fn ($reason, $name) => "{$name} ({$reason})")->implode(', ');

        if ($created === []) {
            return redirect($redirect)->with('error', [
                'title' => 'Niets op de markt gezet',
                'message' => "Alle geselecteerde plots zijn overgeslagen: {$skippedText}.",
            ]);
        }

        $what = $rent ? 'te huur' : 'te koop';
        $message = count($created).' '.(count($created) === 1 ? 'plot staat' : 'plots staan')." nu {$what}: ".implode(', ', $created).'.';

        return redirect($redirect)->with('success', [
            'title' => 'Plots op de markt gezet',
            'message' => $skipped === [] ? $message : "{$message} Overgeslagen: {$skippedText}.",
        ]);
    }

    /** Why a plot cannot be listed this way, or null. */
    protected function skipReason(array $plot, bool $rent): ?string
    {
        $name = $plot['name'];

        if (!PlotService::isTransferable($plot)) {
            return 'mag niet verkocht of verhuurd worden';
        }

        if ($rent) {
            return PlotRentalOfferController::unavailable($plot) ? 'staat al te huur of wordt verhuurd' : null;
        }

        return match (true) {
            PlotListing::where('plot_name', $name)->where('status', 'active')->exists() => 'staat al te koop',
            PlotRentalService::isRented($name) => 'wordt verhuurd',
            default => null,
        };
    }

    protected function validateBulk(Request $request, bool $payoutRequired): array
    {
        return $request->validate([
            'plots' => 'required|array|min:1|max:100',
            'plots.*' => 'required|string|distinct',
            'type' => 'required|in:sale,rent',
            'price' => 'required|numeric|min:0|max:99999999',
            'period' => 'required_if:type,rent|nullable|in:week,month',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:'.Portal::maxUploadKilobytes(),
            'instant_buy' => 'nullable|boolean',
            'payout_bank_account_uuid' => ($payoutRequired ? 'required' : 'nullable').'|string',
        ], [
            'plots.required' => 'Selecteer een of meer plots.',
            'period.required_if' => 'Kies per week of per maand.',
            'payout_bank_account_uuid.required' => 'Kies de bankrekening waarop je het geld wilt ontvangen.',
        ]);
    }

    /** The player's OWNER plots, keyed by name. */
    protected function ownPlots(): Collection
    {
        return collect(auth()->user()->plots)->where('permission', 'OWNER')->keyBy('name');
    }

    protected function allPlots(): Collection
    {
        return collect($this->plotService->getAllPlots())->keyBy('name');
    }
}
