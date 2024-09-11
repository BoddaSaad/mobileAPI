<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['subitem_id', 'user_id', 'order_id', 'date', 'start_time', 'end_time'];

    public function subitem()
    {
        return $this->belongsTo(Subitem::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
