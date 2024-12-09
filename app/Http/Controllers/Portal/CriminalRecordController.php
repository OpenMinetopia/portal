<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;

class CriminalRecordController extends Controller
{
    public function index()
    {
        $records = auth()->user()->criminal_records;

        return view('portal.criminal-records.index', [
            'records' => $records
        ]);
    }
}
