<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PermitType;
use App\Models\PermitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermitsController extends Controller
{
    public function index()
    {
        // Get all active permit types
        $permitTypes = PermitType::where('is_active', true)->get();
        
        // Get user's permit requests
        $requests = PermitRequest::where('user_id', auth()->id())
            ->with(['type', 'handler'])
            ->latest()
            ->get();

        return view('portal.permits.index', compact('permitTypes', 'requests'));
    }

    public function request(PermitType $permitType)
    {
        if (!$permitType->is_active) {
            return back()->with('error', 'Deze vergunning is momenteel niet beschikbaar.');
        }

        return view('portal.permits.request', compact('permitType'));
    }

    public function store(Request $request, PermitType $permitType)
    {
        if (!$permitType->is_active) {
            return back()->with('error', 'Deze vergunning is momenteel niet beschikbaar.');
        }

        try {
            // Build validation rules based on permit type's form fields
            $rules = [];
            $messages = [];

            foreach ($permitType->form_fields as $field) {
                $fieldName = 'form_data.' . $field['label'];
                $rules[$fieldName] = [];

                if ($field['required']) {
                    $rules[$fieldName][] = 'required';
                    $messages[$fieldName.'.required'] = "Het veld '{$field['label']}' is verplicht.";
                } else {
                    $rules[$fieldName][] = 'nullable';
                }

                switch ($field['type']) {
                    case 'number':
                        $rules[$fieldName][] = 'numeric';
                        $messages[$fieldName.'.numeric'] = "Het veld '{$field['label']}' moet een nummer zijn.";
                        break;
                    case 'select':
                        $rules[$fieldName][] = 'in:' . implode(',', $field['options']);
                        $messages[$fieldName.'.in'] = "De geselecteerde optie voor '{$field['label']}' is ongeldig.";
                        break;
                    case 'checkbox':
                        $rules[$fieldName][] = 'boolean';
                        $messages[$fieldName.'.boolean'] = "Het veld '{$field['label']}' moet ja of nee zijn.";
                        break;
                    default:
                        $rules[$fieldName][] = 'string';
                        $messages[$fieldName.'.string'] = "Het veld '{$field['label']}' moet tekst zijn.";
                        break;
                }
            }

            // Convert rules arrays to strings
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = implode('|', $fieldRules);
            }

            // Validate the request
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            // Create the permit request
            $permitRequest = PermitRequest::create([
                'permit_type_id' => $permitType->id,
                'user_id' => auth()->id(),
                'form_data' => $validated['form_data'],
                'status' => 'pending'
            ]);

            return redirect()
                ->route('portal.permits.show', $permitRequest)
                ->with('success', 'Je vergunning aanvraag is succesvol ingediend.');

        } catch (\Exception $e) {
            \Log::error('Error creating permit request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Er is een fout opgetreden bij het indienen van je aanvraag.']);
        }
    }

    public function show(PermitRequest $permitRequest)
    {
        if ($permitRequest->user_id !== auth()->id()) {
            abort(403);
        }

        return view('portal.permits.show', compact('permitRequest'));
    }
} 