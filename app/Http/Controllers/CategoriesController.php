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

    public function show($id){
        $items = Categories::with('items')->findOrFail($id);
        return ItemsResource::collection($items->items);
    }
}
