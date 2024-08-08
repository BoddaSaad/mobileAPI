<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriesResource;
use App\Http\Resources\ItemsResource;
use App\Models\Categories;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index(){
        return CategoriesResource::collection(
            Categories::get()
        );
    }

    public function show(Request $request){
        $query = $request->search ?? "";
        $sort = $request->sort ?? "id";
        $order = $request->order ?? "desc";
        $items = Categories::with(['items' => function($q) use ($query, $sort, $order) {
            $q->select('*', DB::raw('(items.discount / items.price) * 100 as offer'))
                ->withAvg('ratings as rate', 'rating')
                ->where("name", 'like', "%{$query}%")
                ->orWhere("name_ar", 'like', "%{$query}%")
                ->orderBy($sort, $order);
        }])->findOrFail($request->category);
        $offers = Categories::with(['items' => function($q) use ($query, $sort, $order) {
            $q->select('*', DB::raw('(items.discount / items.price) * 100 as offer'))
                ->where('discount', '>', '0')
                ->withAvg('ratings as rate', 'rating')
                ->where("name", 'like', "%{$query}%")
                ->orWhere("name_ar", 'like', "%{$query}%")
                ->where('discount', '>', '0')
                ->orderBy($sort, $order);
        }])->find($request->category);
        $itemCollection = [];
        $itemCollection['items'] = ItemsResource::collection($items->items);
        $itemCollection['offers'] = ItemsResource::collection($offers->items);
        return $itemCollection;
    }
}
