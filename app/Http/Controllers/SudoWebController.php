<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SudoWebController extends Controller
{
    function home()
    {
        return view('sudo.home');
    }
    function provider()
    {
        // defaultdata();
        return view('sudo.provider');
    }
}
