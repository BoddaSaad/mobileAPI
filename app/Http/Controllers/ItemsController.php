<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemsResource;
use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function index(Request $request){
        $query = $request->search ?? "";
        $words = explode(' ',$query);
        $items = Item::with('category')->where(function ($q) use ($words) {
            foreach ($words as $value) {
                $q->orWhere([
                    ['name', 'like', "%{$value}%"],
                ])->orWhere([
                    ['name_ar', 'like', "%{$value}%"],
                ]);
            }
        })->orderBy('id','DESC')->get();
        return ItemsResource::collection($items);
    }

    public function favorites(){
        $item_ids = Auth::user()->favorites()->pluck('item_id');
        return ItemsResource::collection(Item::whereIn('id',$item_ids)->with("category")->paginate(2));
    }

    public function add_favorite(Request $request){
        Favorite::firstOrCreate([
            'item_id'=> $request->item_id,
            'user_id'=> Auth::user()->id
        ],[
            'item_id'=> $request->item_id,
            'user_id'=> Auth::user()->id
        ]);

        return response()->json(['message'=> 'Item has been added successfully to favorites']);
    }

    // TODO change the pagination limit to a proper value
}
