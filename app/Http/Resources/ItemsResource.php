<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $favoriteSatus = false;
        $favorite = Favorite::where('user_id', Auth::id())->where('item_id', $this->id)->first();
        if($favorite){
            $favoriteSatus = true;
        }
        return [
            "id" => $this->id,
            "name" => $this->name,
            "nameAr" => $this->name_ar,
            "description" => $this->description,
            "descriptionAr" => $this->description_ar,
            "discount" => $this->discount,
            "price" => $this->price,
            "location" => $this->location,
            "location_link" => $this->location_link,
            "phone" => $this->phone,
            "createdAt" => $this->created_at,
            "category" => new CategoriesResource($this->category),
            "images" => GalleryResource::collection($this->images),
            "favorite" => $favoriteSatus
        ];
    }
}
