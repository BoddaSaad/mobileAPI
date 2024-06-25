<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoriesResource;
use App\Models\Categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(){
        return CategoriesResource::collection(
            Categories::get()
        );
    }
}
