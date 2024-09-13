<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VoucherController extends Controller
{
    public function check_availability(Request $request){
        $voucher = Voucher::where("code", $request->code)->firstOrFail();
        $usageOfVoucher = Auth()->user()->orders->where('voucher', $request->code)->count();
        if($usageOfVoucher === $voucher->upc){
            return response()->json([
                'message' => 'You have reached the maximum usage limit for this coupon.',
            ], 403);
        }

        if($voucher->quantity === $voucher->used || $voucher->expired || $voucher->expire_date < Carbon::now()){
            return response()->json([
                'message' => 'The Voucher has expired.',
            ], 403);
        }

        if($voucher->fixed_discount){
            $final_price = $request->total_price - $voucher->fixed_discount;
        } else {
            $final_price = $request->total_price - ($request->total_price * $voucher->percent_discount);
        }

        return response()->json([
            'message' => 'The Voucher has been applied!',
            'final_price' => max(0, $final_price)
        ], 200);
    }
}
