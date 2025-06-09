<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CriminalRecordController extends Controller
{
    public function index(Request $request)
    {
        $records = auth()->user()->criminal_records;

        // Check if user wants V2 layout
        $layout = $request->get('layout', 'v2'); // Default to V2
        
        if ($layout === 'v1') {
            return view('portal.criminal-records.index', [
                'records' => $records
            ]);
        }
        
        return view('portal.v2.criminal-records.index', [
            'records' => $records
        ]);
    }
}
