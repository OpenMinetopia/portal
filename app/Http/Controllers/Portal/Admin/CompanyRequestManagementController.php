<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyRequestManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('company.management');
    }
} 