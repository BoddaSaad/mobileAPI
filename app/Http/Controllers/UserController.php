<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request){
        $user = Auth::user();
        $user->name = $request->name;
        $user->save();
        return response()->json(['message'=>'Name has been changed successfully.']);
    }
}
