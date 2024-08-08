<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    protected function hmacCalc($data, $hmac){
        $concatenated = $data['amount_cents'].$data['created_at'].$data['currency'].($data['error_occured'] ? "true" : "false").($data['has_parent_transaction'] ? "true" : "false").$data['id'].$data['integration_id'].($data['is_3d_secure'] ? "true" : "false").($data['is_auth'] ? "true" : "false").($data['is_capture'] ? "true" : "false").($data['is_refunded'] ? "true" : "false").($data['is_standalone_payment'] ? "true" : "false").($data['is_voided'] ? "true" : "false").$data['order']['id'].$data['owner'].($data['pending'] ? "true" : "false").$data['source_data']['pan'].$data['source_data']['sub_type'].$data['source_data']['type'].($data['success'] ? "true" : "false");
        $hashedString = hash_hmac('sha512', $concatenated, config('paymob.hmac_secret'));
        if($hashedString === $hmac){
            return true;
        }
        return false;
    }


    /**
     * Handle the incoming request.
     */
    public function handle(Request $request)
    {
        $data = $request->obj;
        $hmac = $request->query('hmac');
        if($this->hmacCalc($data, $hmac)) {
            $order = Order::where('reference', $data['order']['merchant_order_id'])->firstOrFail();
            if ($data['success'] === true) {
                $order->update(['status' => "Paid"]);
            } else {
                $order->update(['status' => "Failed"]);
            }

            return "Good";
        }

        return "HMAC Isn't Correct";
    }


}
