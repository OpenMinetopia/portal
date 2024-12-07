<?php

namespace App\Http\Controllers;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        return view('docs.index');
    }

    public function authentication()
    {
        return view('docs.authentication');
    }

    public function player()
    {
        return view('docs.player');
    }

    public function police()
    {
        return view('docs.police');
    }

    public function emergency()
    {
        return view('docs.emergency');
    }

    public function plots()
    {
        return view('docs.plots');
    }

    public function economy()
    {
        return view('docs.economy');
    }

    public function vehicles()
    {
        return view('docs.vehicles');
    }

    public function chat()
    {
        return view('docs.chat');
    }

    public function fitness()
    {
        return view('docs.fitness');
    }

    public function teleporters()
    {
        return view('docs.teleporters');
    }

    public function detectionGates()
    {
        return view('docs.detection-gates');
    }

    public function walkieTalkie()
    {
        return view('docs.walkie-talkie');
    }

    public function level()
    {
        return view('docs.level');
    }

    public function bank()
    {
        return view('docs.bank');
    }
} 