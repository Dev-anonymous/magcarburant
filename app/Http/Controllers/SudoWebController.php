<?php

namespace App\Http\Controllers;

use App\Models\Entity;
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
        $item = request('item');
        if ($item) {
            $entity = Entity::where('shortname', $item)->first();
            if ($entity) {
                return view('sudo.apps', compact('entity'));
            }
        }
        $tx = request('tx');
        if ($tx) {
            $entity = Entity::where('shortname', $tx)->first();
            if ($entity) {
                return view('sudo.rates', compact('entity'));
            }
        }

        return view('sudo.provider');
    }

    function rates()
    {
        return view('sudo.structrates');
    }
}
