<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingsController extends Controller
{
    public function get_available_hours(Request $request){
        $dayOfWeek = date('l', strtotime($request->date));
        //$schedules = Schedule::where('item_id', $request->item)->where('day', $dayOfWeek)->get();
        $bookings = Booking::where('subitem_id', $request->subitem)->where('date', $request->date)->get();
        $availableHours = [];
        $start_time = strtotime("00:00:00");
        $end_time = strtotime("24:00:00");
        $slots = [];
        while ($start_time < $end_time) {
            $slot_start_time = date('H:i:s', $start_time);
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
            if ($is_available) {
                $slots[] = $slot_start_time;
            }
            $start_time = strtotime('+1 hour', $start_time);
        }

        $availableHours = array_merge($availableHours, $slots);
        return response()->json(['hours'=> $availableHours]);
    }

    public function booking(Request $request){
        $user_id = Auth::user()->id;
        foreach($request->hours as $hour) {
            $start_time = strtotime($hour);
            $end_time = strtotime('+1 hour', $start_time);
            Booking::create([
                'subitem_id'=> $request->subitem_id,
                'user_id'=> $user_id,
                'date'=> $request->date,
                'start_time'=> date('H:i:s', $start_time),
                'end_time'=> date('H:i:s', $end_time),
                'reference'=> random_int(1000, 99999)
            ]);
        }
        return response()->json(['message'=>'Booking has been completed successfully']);
    }

    public function my_booking()
    {
        $user_id = Auth::user()->id;
        $bookings = Booking::where('user_id', $user_id)->with('item')->get();
        return response()->json($bookings);
    }
}
