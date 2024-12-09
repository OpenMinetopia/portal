<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyRegistryController extends Controller
{
    public function index()
    {
        return view('portal.companies.registry.index');
    }

    public function search(Request $request)
    {
        $query = Company::query()
            ->with(['type', 'owner']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('kvk_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type_id', $type);
        }

        if ($status = $request->input('status')) {
            $isActive = $status === 'active';
            $query->where('is_active', $isActive);
        }

        return $query->paginate(10);
    }

    public function show(Company $company)
    {
        $company->load(['type', 'owner', 'dissolutionRequest']);

        return view('portal.companies.registry.show', compact('company'));
    }
}
