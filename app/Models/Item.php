<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [

    ];

    public function images(){
        return $this->hasMany(Gallery::class);
    }

    public function category(){
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }

    public function subitems(){
        return $this->hasMany(Subitem::class);
    }
}
