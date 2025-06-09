<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Plugin\PlotService;
use App\Services\MojangApiService;
use Illuminate\Http\Request;

class AdminPlotController extends Controller
{
    protected PlotService $plotService;
    protected MojangApiService $mojangApi;

    public function __construct(PlotService $plotService, MojangApiService $mojangApi)
    {
        $this->plotService = $plotService;
        $this->mojangApi = $mojangApi;
    }

    public function index()
    {
        $plots = $this->plotService->getAllPlots();

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.admin.plots.index', [
                'plots' => $plots
            ]);
        }

        return view('portal.v2.admin.plots.index', [
            'plots' => $plots
        ]);
    }

    public function show(string $name)
    {
        \Log::info('Fetching plot details', ['name' => $name]);
        
        $plot = $this->plotService->getPlot($name);
        
        if (!$plot) {
            \Log::error('Plot not found', ['name' => $name]);
            abort(404);
        }

        \Log::info('Plot details retrieved', ['plot' => $plot]);

        // Get all verified users for the selection dropdowns
        $users = User::where('minecraft_verified', true)->get();

        // Determine layout version
        $layout = request()->get('layout', 'v2'); // Default to v2, fallback to v1

        if ($layout === 'v1') {
            return view('portal.admin.plots.show', [
                'plot' => $plot,
                'users' => $users
            ]);
        }

        return view('portal.v2.admin.plots.show', [
            'plot' => $plot,
            'users' => $users
        ]);
    }

    public function addOwner(Request $request, string $name)
    {
        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        \Log::info('Attempting to add owner to plot', [
            'plot' => $name,
            'user_uuid' => $validated['user_uuid']
        ]);

        $success = $this->plotService->addOwner($name, $validated['user_uuid']);

        if (!$success) {
            \Log::error('Failed to add owner to plot', [
                'plot' => $name,
                'user_uuid' => $validated['user_uuid']
            ]);

            return back()->with('error', [
                'title' => 'Actie mislukt',
                'message' => 'Het toevoegen van de eigenaar is mislukt. Controleer of de speler al eigenaar is.'
            ]);
        }

        return back()->with('success', [
            'title' => 'Eigenaar toegevoegd',
            'message' => 'De eigenaar is succesvol toegevoegd aan het plot.'
        ]);
    }

    public function removeOwner(Request $request, string $name)
    {
        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        $success = $this->plotService->removeOwner($name, $validated['user_uuid']);

        if (!$success) {
            return back()->with('error', [
                'title' => 'Actie mislukt',
                'message' => 'Het verwijderen van de eigenaar is mislukt.'
            ]);
        }

        return back()->with('success', [
            'title' => 'Eigenaar verwijderd',
            'message' => 'De eigenaar is succesvol verwijderd van het plot.'
        ]);
    }

    public function addMember(Request $request, string $name)
    {
        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        \Log::info('Attempting to add member to plot', [
            'plot' => $name,
            'user_uuid' => $validated['user_uuid']
        ]);

        $success = $this->plotService->addMember($name, $validated['user_uuid']);

        if (!$success) {
            \Log::error('Failed to add member to plot', [
                'plot' => $name,
                'user_uuid' => $validated['user_uuid']
            ]);

            return back()->with('error', [
                'title' => 'Actie mislukt',
                'message' => 'Het toevoegen van het lid is mislukt. Controleer of de speler al lid is.'
            ]);
        }

        return back()->with('success', [
            'title' => 'Lid toegevoegd',
            'message' => 'Het lid is succesvol toegevoegd aan het plot.'
        ]);
    }

    public function removeMember(Request $request, string $name)
    {
        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        $success = $this->plotService->removeMember($name, $validated['user_uuid']);

        if (!$success) {
            return back()->with('error', [
                'title' => 'Actie mislukt',
                'message' => 'Het verwijderen van het lid is mislukt.'
            ]);
        }

        return back()->with('success', [
            'title' => 'Lid verwijderd',
            'message' => 'Het lid is succesvol verwijderd van het plot.'
        ]);
    }
} 