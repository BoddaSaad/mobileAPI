<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function update(Request $request){
        $user = Auth::user();
        $user->name = $request->name;
        $user->save();
        return response()->json(['message'=>'Name has been changed successfully.']);
    }

    public function delete_account(Request $request){
        $user = auth()->user();
        if(!Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        Booking::where('user_id', $user->id)->delete();
        Favorite::where('user_id', $user->id)->delete();
        Rating::where('user_id', $user->id)->delete();
        $user->delete();
        return response()->json(['message'=>'Your email has been deleted successfully.']);
    }
}
