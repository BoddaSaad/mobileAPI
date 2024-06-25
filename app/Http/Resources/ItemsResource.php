<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => (string) $this->id,
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
            "images" => GalleryResource::collection($this->images)
        ];
    }
}
