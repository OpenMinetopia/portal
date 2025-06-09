<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Plugin\PlotService;
use Illuminate\Http\Request;

class PlotController extends Controller
{
    protected PlotService $plotService;

    public function __construct(PlotService $plotService)
    {
        $this->plotService = $plotService;
    }

    public function index(Request $request)
    {
        $plots = auth()->user()->plots;
        
        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.plots.index', [
                'plots' => $plots
            ]);
        }
        
        return view('portal.v2.plots.index', [
            'plots' => $plots
        ]);
    }

    public function show(Request $request, string $name)
    {
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $name);

        if (!$plot) {
            abort(404);
        }

        // Only get users list if the current user is an owner
        $users = $plot['permission'] === 'OWNER' 
            ? User::where('minecraft_verified', true)
                ->where('id', '!=', auth()->id()) // Exclude current user
                ->get()
            : collect();

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.plots.show', [
                'plot' => $plot,
                'users' => $users,
                'isOwner' => $plot['permission'] === 'OWNER'
            ]);
        }

        return view('portal.v2.plots.show', [
            'plot' => $plot,
            'users' => $users,
            'isOwner' => $plot['permission'] === 'OWNER'
        ]);
    }

    public function addOwner(Request $request, string $name)
    {
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $name);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je hebt geen rechten om eigenaren toe te voegen aan dit plot.'
            ]);
        }

        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        $success = $this->plotService->addOwner($name, $validated['user_uuid']);

        if (!$success) {
            return back()->with('error', [
                'title' => 'Actie mislukt',
                'message' => 'Het toevoegen van de eigenaar is mislukt.'
            ]);
        }

        return back()->with('success', [
            'title' => 'Eigenaar toegevoegd',
            'message' => 'De eigenaar is succesvol toegevoegd aan het plot.'
        ]);
    }

    public function removeOwner(Request $request, string $name)
    {
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $name);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je hebt geen rechten om eigenaren te verwijderen van dit plot.'
            ]);
        }

        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        // Prevent removing yourself as owner
        if ($validated['user_uuid'] === auth()->user()->minecraft_plain_uuid) {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je kunt jezelf niet verwijderen als eigenaar.'
            ]);
        }

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
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $name);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je hebt geen rechten om leden toe te voegen aan dit plot.'
            ]);
        }

        $validated = $request->validate([
            'user_uuid' => 'required|string'
        ]);

        $success = $this->plotService->addMember($name, $validated['user_uuid']);

        if (!$success) {
            return back()->with('error', [
                'title' => 'Actie mislukt',
                'message' => 'Het toevoegen van het lid is mislukt.'
            ]);
        }

        return back()->with('success', [
            'title' => 'Lid toegevoegd',
            'message' => 'Het lid is succesvol toegevoegd aan het plot.'
        ]);
    }

    public function removeMember(Request $request, string $name)
    {
        $plots = auth()->user()->plots;
        $plot = collect($plots)->firstWhere('name', $name);

        if (!$plot || $plot['permission'] !== 'OWNER') {
            return back()->with('error', [
                'title' => 'Actie niet mogelijk',
                'message' => 'Je hebt geen rechten om leden te verwijderen van dit plot.'
            ]);
        }

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