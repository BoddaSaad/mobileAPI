<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriesResource;
use App\Http\Resources\ItemsResource;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(){
        return CategoriesResource::collection(
            Categories::get()
        );
    }

    public function show(Request $request){
        $items = Categories::with('items')->findOrFail($request->category);
        $offers = Categories::with(['items' => function($q) {
            $q->where('discount', '>', '0');
        }])->find($request->category);
        $itemCollection = [];
        $itemCollection['items'] = ItemsResource::collection($items->items);
        $itemCollection['offers'] = ItemsResource::collection($offers->items);
        return $itemCollection;
    }
}
