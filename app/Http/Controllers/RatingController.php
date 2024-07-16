<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function rate(Request $request){
        Rating::create([
            'user_id'=> Auth::user()->id,
            'item_id'=> $request->item,
            'review'=> $request->review ?? null,
            'rating'=> $request->rating
        ]);
        return response()->json(['message'=>'Rating has been successfully created.']);
    }

    public function show_ratings(Request $request){
        $ratings = Rating::where('item_id', $request->item)->with('user')->get();
        return $ratings;
    }
}
