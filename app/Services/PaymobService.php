<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PaymobService
{
    protected $secret;
    protected $methods;

    public function __construct()
    {
        $this->secret = config('paymob.secret_key');
        $this->methods = config('paymob.methods');
    }

    public function intent($total_price_cents, $reference){
        $user = Auth::user();
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->secret,
            'Content-Type' => 'application/json'
        ])->post('https://accept.paymob.com/v1/intention/', [
            "amount"=> $total_price_cents,
            "currency"=> "EGP",
            "special_reference"=>$reference,
            "notification_url"=> "https://webhook.site/0932871c-2208-4648-b478-0389dffc440f",
            "payment_methods"=> $this->methods,
            "billing_data"=>[
                "first_name"=>$user->name,
                "last_name"=>"NA",
                "email"=>$user->email,
                "phone_number"=>$user->phone
            ]
        ]);

        return $response;
    }
}
