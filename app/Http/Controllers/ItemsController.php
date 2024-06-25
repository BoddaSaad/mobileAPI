<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemsResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(){
        return ItemsResource::collection(Item::with("category")->paginate(2));
    }

    // TODO change the pagination limit to a proper value
}
