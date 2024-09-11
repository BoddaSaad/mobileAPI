<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Item;
use App\Models\Order;
use App\Models\Schedule;
use App\Services\PaymobService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BookingsController extends Controller
{
    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    public function get_available_hours(Request $request){
        $bookings = Booking::where('subitem_id', $request->subitem)->where('date', $request->date)->whereRelation('order', 'status', 'Paid')->get();
        $availableHours = [];
        $start_time = strtotime("00:00:00");
        $end_time = strtotime("24:00:00");
        $slots = [];
        while ($start_time < $end_time) {
            $slot_start_time = date('h:i A', $start_time);
            $slot_end_time = date('H:i:s', strtotime('+1 hour', $start_time));
            $is_available = true;
            foreach ($bookings as $appointment) {
                $appointment_start = strtotime($appointment['start_time']);
                $appointment_end = strtotime($appointment['end_time']);

                // Check if slot conflicts with any appointment
                if (($start_time < $appointment_end) && ($appointment_start < strtotime($slot_end_time))) {
                    $is_available = false;
                    break;
                }
            }
            $start_time = strtotime('+1 hour', $start_time);

            if ($is_available) {
                $slots[] = $slot_start_time ." - ". date('h:i A', $start_time);
            }
        }

        $availableHours = array_merge($availableHours, $slots);
        return response()->json(['hours'=> $availableHours]);
    }

    public function booking(Request $request){
        $user = Auth::user();
        $item = Item::whereHas('subitems', function (Builder $query) use ($request) {
            $query->where('id', $request->subitem_id);
        })->first();
        $price = $item->price - $item->discount;
        $total_price_cents = $price * count($request->hours) * 100;

        $order = Order::create([
            'reference'=> random_int(1000, 99999),
            'status'=> "Awaiting Payment",
            'price' => $total_price_cents,
        ]);

        foreach($request->hours as $hour) {
            $start_time = strtotime(date('H:i:s', strtotime($hour)));
            $end_time = strtotime('+1 hour', $start_time);
            if(Booking::where("date", $request->date)->where("start_time", date('H:i:s', $start_time))->exists()){
                continue;
            }

            Booking::create([
                'subitem_id'=> $request->subitem_id,
                'user_id'=> $user->id,
                'order_id'=> $order->id,
                'date'=> $request->date,
                'start_time'=> date('H:i:s', $start_time),
                'end_time'=> date('H:i:s', $end_time)
            ]);
        }

        $response = $this->paymobService->intent($total_price_cents, $order->reference);
        $order->update(['transaction'=>$response['id']]);
        return response()->json([
            'message'=>'Redirect the user to this url',
            'url'=>'https://accept.paymob.com/unifiedcheckout/?publicKey='.config('paymob.public_key').'&clientSecret='.$response['client_secret']
        ]);
    }

    public function my_booking()
    {
        $user_id = Auth::user()->id;
        $bookings = Booking::where('user_id', $user_id)->with(['subitem' => function ($q) {
            $q->with('item');
        }])->get();
        return response()->json($bookings);
    }
}
