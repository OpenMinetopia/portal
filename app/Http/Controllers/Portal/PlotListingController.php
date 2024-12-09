<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PlotListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlotListingController extends Controller
{
    public function index()
    {
        $listings = PlotListing::with('seller')
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('portal.plots.listings.index', [
            'listings' => $listings
        ]);
    }

    public function create(string $plotName)
    {
        // Check if user owns this plot
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $plotName);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return redirect()->route('portal.plots.show', $plotName)
                ->with('error', 'Je kunt alleen je eigen plots verkopen.');
        }

        // Check if plot is already listed
        if (PlotListing::where('plot_name', $plotName)->where('status', 'active')->exists()) {
            return redirect()->route('portal.plots.show', $plotName)
                ->with('error', 'Dit plot staat al te koop.');
        }

        return view('portal.plots.listings.create', [
            'plot' => $plot
        ]);
    }

    public function store(Request $request, string $plotName)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'instant_buy' => 'nullable|boolean'
        ]);

        // Get plot details from user's plots
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $plotName);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return redirect()->route('portal.plots.show', $plotName)
                ->with('error', [
                    'title' => 'Actie niet mogelijk',
                    'message' => 'Je kunt alleen je eigen plots verkopen.'
                ]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('plot-listings', 'public');
        }

        PlotListing::create([
            'plot_name' => $plotName,
            'seller_id' => auth()->id(),
            'price' => $validated['price'],
            'description' => $validated['description'],
            'image_path' => $imagePath,
            'min_x' => $plot['location']['min']['x'],
            'min_y' => $plot['location']['min']['y'],
            'min_z' => $plot['location']['min']['z'],
            'max_x' => $plot['location']['max']['x'],
            'max_y' => $plot['location']['max']['y'],
            'max_z' => $plot['location']['max']['z'],
            'instant_buy' => $request->has('instant_buy')
        ]);

        return redirect()->route('portal.plots.show', $plotName)
            ->with('success', [
                'title' => 'Plot te koop gezet!',
                'message' => 'Je plot staat nu te koop. Zodra iemand je plot koopt, wordt deze automatisch overgedragen.'
            ]);
    }

    public function showBuyForm(PlotListing $listing)
    {
        // Add validation checks
        if ($listing->seller_id === auth()->id()) {
            return redirect()->route('portal.plots.listings.index')
                ->with('error', [
                    'title' => 'Actie niet mogelijk',
                    'message' => 'Je kunt je eigen plot niet kopen.'
                ]);
        }

        if ($listing->status !== 'active') {
            return redirect()->route('portal.plots.listings.index')
                ->with('error', [
                    'title' => 'Plot niet beschikbaar',
                    'message' => 'Dit plot is niet meer beschikbaar voor aankoop.'
                ]);
        }

        if (!$listing->instant_buy) {
            return redirect()->route('portal.plots.listings.index')
                ->with('error', [
                    'title' => 'Direct kopen niet mogelijk',
                    'message' => 'De verkoper heeft aangegeven eerst contact te willen hebben met potentiële kopers.'
                ]);
        }

        return view('portal.plots.listings.buy', [
            'listing' => $listing
        ]);
    }

    public function buy(PlotListing $listing)
    {
        if ($listing->seller_id === auth()->id()) {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je kunt je eigen plot niet kopen.'
            ]);
        }

        if ($listing->status !== 'active') {
            return back()->with('error', [
                'title' => 'Plot niet beschikbaar',
                'message' => 'Dit plot is niet meer beschikbaar voor aankoop.'
            ]);
        }

        // Check if user has enough balance
        if (auth()->user()->balance < $listing->price) {
            return back()->with('error', [
                'title' => 'Onvoldoende saldo',
                'message' => 'Je hebt onvoldoende saldo om dit plot te kopen.'
            ]);
        }

        // TODO: CREATE BUYING LOGIC
        // 1. Transfer money from buyer to seller
        // 2. Transfer plot ownership
        // 3. Send notifications to both parties

        $listing->update(['status' => 'sold']);

        return redirect()->route('portal.plots.listings.index')
            ->with('success', [
                'title' => 'Plot gekocht!',
                'message' => 'Je hebt het plot succesvol gekocht. Het plot wordt automatisch aan je overgedragen.'
            ]);
    }

    public function destroy(PlotListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je kunt alleen je eigen listings verwijderen.'
            ]);
        }

        $listing->update(['status' => 'cancelled']);

        return redirect()->route('portal.plots.show', $listing->plot_name)
            ->with('success', [
                'title' => 'Plot van de markt gehaald',
                'message' => 'Je plot staat niet meer te koop en is van de markt gehaald.'
            ]);
    }
}
