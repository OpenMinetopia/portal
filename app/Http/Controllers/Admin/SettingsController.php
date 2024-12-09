<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalFeature;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $features = PortalFeature::all();
        
        return view('portal.admin.settings.index', compact('features'));
    }

    public function updateFeatures(Request $request)
    {
        $features = PortalFeature::all();
        
        foreach ($features as $feature) {
            $feature->update([
                'is_enabled' => $request->has("features.{$feature->key}")
            ]);
        }

        return redirect()->route('portal.admin.settings.index')
            ->with('success', 'Instellingen zijn succesvol bijgewerkt.');
    }
} 