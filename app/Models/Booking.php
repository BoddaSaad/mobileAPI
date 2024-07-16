<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['subitem_id', 'user_id', 'date', 'start_time', 'end_time', 'reference'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
