<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProviderWebController extends Controller
{
    function home()
    {
        return view('provider.home');
    }

    function apps()
    {
        $entity = auth()->user()->entities()->first();
        return view('provider.apps', compact('entity'));
    }

    function rates()
    {
        return view('provider.rates');
    }
}
