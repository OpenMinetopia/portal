<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PlotRental;
use App\Models\PlotRentalOffer;
use App\Services\PlotRentalException;
use App\Services\PlotRentalService;
use Illuminate\Http\Request;

/**
 * "Mijn huur": the plots a player rents and the ones they let. Outside the
 * broker feature, so a rental can always be seen and ended.
 */
class PlotRentalController extends Controller
{
    public function __construct(protected PlotRentalService $rentals)
    {
    }

    public function index()
    {
        $user = auth()->user();

        return view('portal.plots.rentals.index', [
            'renting' => PlotRental::where('renter_id', $user->id)->with('offer')->latest('started_at')->take(50)->get(),
            'letting' => PlotRental::landlordView($user)->with('renter')->orderByRaw("status = 'active' desc")->latest('started_at')->take(100)->get(),
            'offers' => PlotRentalOffer::where('status', 'active')
                ->where(fn ($query) => $query->where('landlord_id', $user->id)->when(PlotRental::managesServerRentals($user), fn ($query) => $query->orWhere('is_server_listing', true)))
                ->latest()->get(),
            'bankAccounts' => $user->bank_accounts,
        ]);
    }

    public function cancel(PlotRental $rental)
    {
        $user = auth()->user();

        $by = match (true) {
            $rental->isRenter($user) => 'renter',
            $rental->isLandlord($user) => 'landlord',
            default => abort(403),
        };

        if (!$rental->isActive() || $rental->ends_at) {
            return back()->with('error', ['title' => 'Actie niet mogelijk', 'message' => 'Deze huur is al opgezegd of beëindigd.']);
        }

        $this->rentals->cancel($rental, $by);
        $rental->refresh();

        return back()->with('success', [
            'title' => 'Huur opgezegd',
            'message' => $rental->isActive()
                ? "De huur eindigt op {$rental->ends_at->format('d-m-Y H:i')}. Tot die tijd blijft de huurder lid van het plot."
                : 'De huur is beëindigd.',
        ]);
    }

    /** The renter picks the account the rent comes from; while the rent is overdue this pays it right away. */
    public function pay(Request $request, PlotRental $rental)
    {
        abort_unless($rental->isRenter(auth()->user()), 403);

        $validated = $request->validate(['renter_bank_account_uuid' => 'required|string']);

        if (!$rental->isActive()) {
            return back()->with('error', ['title' => 'Actie niet mogelijk', 'message' => 'Deze huur is beëindigd.']);
        }

        try {
            $result = $this->rentals->payNow($rental, $validated['renter_bank_account_uuid']);
        } catch (PlotRentalException $e) {
            return back()->with('error', $e->flash());
        }

        return match ($result) {
            'renewed' => back()->with('success', ['title' => 'Huur betaald', 'message' => "Bedankt! De huur van {$rental->plot_name} is betaald."]),
            'overdue', 'evicted' => back()->with('error', ['title' => 'Betaling mislukt', 'message' => 'Er staat niet genoeg geld op deze rekening. Zet er geld op of kies een andere rekening.']),
            'skipped' => back()->with('error', ['title' => 'Betaling wordt verwerkt', 'message' => 'Er loopt al een betaling voor deze huur. Neem contact op met een administrator als dit zo blijft.']),
            default => back()->with('success', ['title' => 'Rekening gewijzigd', 'message' => 'De volgende huur wordt van deze rekening afgeschreven.']),
        };
    }
}
