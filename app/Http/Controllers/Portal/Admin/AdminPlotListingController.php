<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlotListing;
use App\Services\Plugin\PlotService;
use Illuminate\Http\Request;

class AdminPlotListingController extends Controller
{
    protected PlotService $plotService;

    public function __construct(PlotService $plotService)
    {
        $this->plotService = $plotService;
    }

    public function create(string $name)
    {
        $plot = $this->plotService->getPlot($name);

        if (!$plot) {
            abort(404);
        }

        if (PlotListing::where('plot_name', $name)->where('status', 'active')->exists()) {
            return redirect()->route('portal.admin.plots.show', $name)
                ->with('error', [
                    'title' => 'Actie niet mogelijk',
                    'message' => 'Dit plot staat al te koop.'
                ]);
        }

        return view('portal.plots.listings.create', [
            'plot' => $plot,
            'isServerListing' => true,
            'storeUrl' => route('portal.admin.plots.listings.store', $name),
            'backUrl' => route('portal.admin.plots.show', $name),
        ]);
    }

    public function store(Request $request, string $name)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'instant_buy' => 'nullable|boolean',
            'payout_bank_account_uuid' => 'nullable|string'
        ]);

        $plot = $this->plotService->getPlot($name);

        if (!$plot) {
            abort(404);
        }

        if (PlotListing::where('plot_name', $name)->where('status', 'active')->exists()) {
            return redirect()->route('portal.admin.plots.show', $name)
                ->with('error', [
                    'title' => 'Actie niet mogelijk',
                    'message' => 'Dit plot staat al te koop.'
                ]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('plot-listings', 'public');
        }

        PlotListing::create([
            'plot_name' => $name,
            'seller_id' => auth()->id(),
            'is_server_listing' => true,
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
            'instant_buy' => $request->has('instant_buy')
        ]);

        return redirect()->route('portal.admin.plots.show', $name)
            ->with('success', [
                'title' => 'Plot te koop gezet!',
                'message' => 'Het plot staat nu bij de makelaar. Zodra iemand het koopt, wordt het automatisch overgedragen.'
            ]);
    }
}
