<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemsResource;
use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemsController extends Controller
{
    public function index(Request $request){
        $query = $request->search ?? "";
        $sort = $request->sort ?? "id";
        $order = $request->order ?? "desc";
        $items = Item::withAvg('ratings as rate', 'rating')->with('category')
            ->select('*', DB::raw('(items.discount / items.price) * 100 as offer'))
            ->where("name", 'like', "%{$query}%")
            ->orWhere("name_ar", 'like', "%{$query}%")
            ->orderBy($sort, $order)
            ->get();
        return ItemsResource::collection($items);
    }

    public function show(Request $request){
        $item = Item::findOrFail($request->item);
        return new ItemsResource($item);
    }

    public function favorites(){
        $item_ids = Auth::user()->favorites()->pluck('item_id');
        return ItemsResource::collection(Item::whereIn('id',$item_ids)->with("category")->get());
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

    public function delete_favorite(Request $request){
        Favorite::where('item_id', $request->item_id)->where('user_id', Auth::user()->id)->delete();
        return response()->json(['message'=> 'Item has been removed successfully from favorites']);
    }

    public function delete_all_favorites(Request $request){
        Favorite::where('user_id', Auth::user()->id)->delete();
        return response()->json(['message'=> 'All Items have been removed successfully from favorites']);
    }

    // TODO change the pagination limit to a proper value
}
