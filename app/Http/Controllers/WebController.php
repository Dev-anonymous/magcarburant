<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebController extends Controller
{
    function applogs()
    {
        // abort_if(!canViewLog(), 403, "Not authorized");
        return view('applogs');
    }
}
