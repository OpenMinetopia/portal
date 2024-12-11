<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalFeature;
use App\Models\PermitSetting;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('portal.admin.settings.index', [
            'features' => PortalFeature::all(),
            'permitSettings' => PermitSetting::first() ?? PermitSetting::create([]),
            'companySettings' => CompanySetting::first() ?? CompanySetting::create([])
        ]);
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
            ->with('success', 'Portal functies zijn succesvol bijgewerkt.');
    }

    public function updatePermitSettings(Request $request)
    {
        $validated = $request->validate([
            'payout_bank_account_uuid' => 'required|string'
        ], [
            'payout_bank_account_uuid.required' => 'Selecteer een bankrekening voor vergunningsbetalingen.'
        ]);

        $settings = PermitSetting::first() ?? new PermitSetting();
        $settings->payout_bank_account_uuid = $validated['payout_bank_account_uuid'];
        $settings->save();

        return back()->with('success', 'Vergunningsinstellingen zijn succesvol bijgewerkt.');
    }

    public function updateCompanySettings(Request $request)
    {
        $validated = $request->validate([
            'payout_bank_account_uuid' => 'required|string'
        ], [
            'payout_bank_account_uuid.required' => 'Selecteer een bankrekening voor bedrijfsaanvragen.'
        ]);

        $settings = CompanySetting::first() ?? new CompanySetting();
        $settings->payout_bank_account_uuid = $validated['payout_bank_account_uuid'];
        $settings->save();

        return back()->with('success', 'Bedrijfsinstellingen zijn succesvol bijgewerkt.');
    }
} 