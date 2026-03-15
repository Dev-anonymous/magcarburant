<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class WebController extends Controller
{
    function applogs()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'logisticien', 'sudo']), 403, "No permission");
        return view('applogs');
    }

    function roles()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'logisticien']), 403, "No permission");
        $permissions = Permission::where('user_role', $user->user_role)->orderBy('name')->get();
        return view('roles', compact('permissions'));
    }

    function users()
    {
        $user = request()->user();
        abort_if(!in_array($user->user_role, ['petrolier', 'etatique', 'logisticien']), 403, "No permission");
        $permissions = Permission::where('user_role', $user->user_role)->orderBy('name')->get();
        return '...';
        return view('roles', compact('permissions'));
    }
}
