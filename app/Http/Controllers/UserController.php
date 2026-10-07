<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function updateSidebar(Request $request)
    {
        $user = Auth::user();
        $user->sidebar_collapsed = $request->boolean('collapsed');
        $user->save();

        return response()->json(['success' => true]);
    }
}