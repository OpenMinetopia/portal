<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Portal\PlotRentalOfferController;
use App\Services\Plugin\PlotService;
use Illuminate\Http\Request;

/** Admins offer any plot for rent on behalf of the server; the payout account is optional, as with sales. */
class AdminPlotRentalOfferController extends Controller
{
    public function __construct(protected PlotService $plotService)
    {
    }

    public function create(string $name)
    {
        $plot = $this->plotService->getPlot($name) ?? abort(404);

        if ($error = PlotRentalOfferController::unavailable($name)) {
            return redirect()->route('portal.admin.plots.show', $name)->with('error', $error);
        }

        return view('portal.plots.rentals.create', [
            'plot' => $plot,
            'isServerListing' => true,
            'storeUrl' => route('portal.admin.plots.rentals.store', $name),
            'backUrl' => route('portal.admin.plots.show', $name),
        ]);
    }

    public function store(Request $request, string $name)
    {
        $validated = PlotRentalOfferController::validateOffer($request, payoutRequired: false);

        $plot = $this->plotService->getPlot($name) ?? abort(404);

        if ($error = PlotRentalOfferController::unavailable($name)) {
            return redirect()->route('portal.admin.plots.show', $name)->with('error', $error);
        }

        PlotRentalOfferController::createOffer($request, $validated, $plot, isServerListing: true);

        return redirect()->route('portal.admin.plots.show', $name)
            ->with('success', [
                'title' => 'Plot te huur gezet!',
                'message' => 'Het plot staat nu te huur bij de makelaar. Een huurder wordt lid van het plot.',
            ]);
    }
}
