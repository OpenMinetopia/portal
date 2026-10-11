<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Services\Plugin\VogService;

class CriminalRecordController extends Controller
{
    public function index(VogService $vogs)
    {
        $records = auth()->user()->criminal_records;

        return view('portal.criminal-records.index', [
            'records' => $records,
            'vogs' => auth()->user()->minecraft_plain_uuid ? $vogs->forPlayer(auth()->user()->minecraft_plain_uuid) : null,
        ]);
    }
}
