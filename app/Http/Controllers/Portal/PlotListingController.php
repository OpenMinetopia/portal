<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PlotListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\Plugin\BankingService;
use App\Services\Plugin\PlotService;
use App\Notifications\PlotTransactionNotification;

class PlotListingController extends Controller
{
    protected BankingService $bankingService;
    protected PlotService $plotService;

    public function __construct(BankingService $bankingService, PlotService $plotService)
    {
        $this->bankingService = $bankingService;
        $this->plotService = $plotService;
    }

    public function index()
    {
        $listings = PlotListing::with('seller')
            ->where('status', 'active')
            ->latest()
            ->get();

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.plots.listings.index', [
                'listings' => $listings
            ]);
        }

        return view('portal.v2.plots.listings.index', [
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

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.plots.listings.create', [
                'plot' => $plot
            ]);
        }

        return view('portal.v2.plots.listings.create', [
            'plot' => $plot
        ]);
    }

    public function store(Request $request, string $plotName)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
            'instant_buy' => 'nullable|boolean',
            'payout_bank_account_uuid' => 'required|string'
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
            'payout_bank_account_uuid' => $validated['payout_bank_account_uuid'],
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
        // Only prevent non-admins from buying their own plots
        if ($listing->seller_id === auth()->id() && !auth()->user()->isAdmin()) {
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

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.plots.listings.buy', [
                'listing' => $listing
            ]);
        }

        return view('portal.v2.plots.listings.buy', [
            'listing' => $listing
        ]);
    }

    public function buy(Request $request, PlotListing $listing)
    {
        $validated = $request->validate([
            'buyer_bank_account_uuid' => 'required|string',
            'terms' => 'required|accepted'
        ], [
            'buyer_bank_account_uuid.required' => 'Selecteer een bankrekening om mee te betalen.',
            'terms.accepted' => 'Je moet akkoord gaan met de voorwaarden.'
        ]);

        if ($listing->seller_id === auth()->id() && !auth()->user()->isAdmin()) {
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

        // Get buyer's bank account
        $buyerAccount = collect(auth()->user()->bank_accounts)
            ->firstWhere('uuid', $validated['buyer_bank_account_uuid']);

        if (!$buyerAccount || $buyerAccount['balance'] < $listing->price) {
            return back()->with('error', [
                'title' => 'Onvoldoende saldo',
                'message' => 'De geselecteerde bankrekening heeft onvoldoende saldo.'
            ]);
        }

        try {
            // Withdraw from buyer's account
            $withdrawSuccess = $this->bankingService->withdraw(
                $validated['buyer_bank_account_uuid'],
                $listing->price
            );

            if (!$withdrawSuccess) {
                throw new \Exception('Failed to withdraw money from buyer account');
            }

            // Deposit to seller's account
            $depositSuccess = $this->bankingService->deposit(
                $listing->payout_bank_account_uuid,
                $listing->price
            );

            if (!$depositSuccess) {
                // Rollback withdrawal if deposit fails
                $this->bankingService->deposit(
                    $validated['buyer_bank_account_uuid'],
                    $listing->price
                );
                throw new \Exception('Failed to deposit money to seller account');
            }

            // Transfer plot ownership
            $transferSuccess = $this->plotService->transferOwnership(
                $listing->plot_name,
                auth()->user()->minecraft_plain_uuid
            );

            if (!$transferSuccess) {
                // Rollback the transaction if plot transfer fails
                $this->bankingService->withdraw(
                    $listing->payout_bank_account_uuid,
                    $listing->price
                );
                $this->bankingService->deposit(
                    $validated['buyer_bank_account_uuid'],
                    $listing->price
                );
                throw new \Exception('Het overzetten van het plot is mislukt. De betaling is teruggestort.');
            }

            // Update listing
            $listing->update([
                'status' => 'sold',
                'buyer_bank_account_uuid' => $validated['buyer_bank_account_uuid']
            ]);

            // Send notifications to both buyer and seller
            $listing->seller->notify(new PlotTransactionNotification($listing, 'sold'));
            auth()->user()->notify(new PlotTransactionNotification($listing, 'bought'));

            return redirect()->route('portal.plots.listings.index')
                ->with('success', [
                    'title' => 'Plot gekocht!',
                    'message' => 'Je hebt het plot succesvol gekocht. Het plot wordt automatisch aan je overgedragen.'
                ]);

        } catch (\Exception $e) {
            \Log::error('Plot purchase failed', [
                'plot' => $listing->plot_name,
                'buyer' => auth()->user()->minecraft_plain_uuid,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', [
                'title' => 'Er ging iets mis',
                'message' => $e->getMessage() . ' Neem contact op met een administrator als dit probleem zich blijft voordoen.'
            ]);
        }
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
