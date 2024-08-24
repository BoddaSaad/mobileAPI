<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Mail\VerificationCode;
use App\Models\Otp;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            // 'verifyCode' => random_int(10000, 99999)
        ]);

        $otp = Otp::firstOrCreate([
            'email'=> $request->email,
            'usage'=> 0,
            'used'=> 0
        ],[
            'otp'=> random_int(10000, 99999),
            'email'=> $request->email,
            'usage'=> 0,
        ]);

        Mail::to($user)->send(new VerificationCode(
            $otp->otp, "Use this verification code to verify your email"
        ));

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'version'=> config('app.version'),
            'token' => $user->createToken("API Token of {$user->email}")->plainTextToken
        ], 201);
    }

    public function verify_email(Request $request){
        $otp = Otp::where([
            'otp'=> $request->otp,
            'used'=> 0,
            'usage'=> 0,
            'email'=> $request->email
        ])->firstOrFail();
        $otp->used = 1;
        $otp->save();

        $user = User::where('email', $request->email)->first();
        $user->markEmailAsVerified();

        return response()->json([
            'message'=> 'Email has been verified successfully.'
        ]);
    }
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        if(!$user->hasVerifiedEmail()){
            return response()->json([
                'message'=>'This Email is not verified yet'
            ], 403);
        }
        return response()->json([
            'message' => 'User authenticated successfully',
            'user' => $user,
            'version'=> config('app.version'),
            'token' => $user->createToken("API Token of {$user->email}")->plainTextToken
        ], 200);

    }

    public function generate_code(Request $request){
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json(['message'=> 'No such email'], 404);
        }
        $otp = Otp::firstOrCreate([
            'email'=> $request->email,
            'usage'=> 1,
            'used'=> 0
        ],[
            'otp'=> random_int(10000, 99999),
            'email'=> $request->email,
            'usage'=> 1,
        ]);

        Mail::to($user)->send(new VerificationCode(
                $otp->otp, "Use this verification code to reset your password"
            ));

        return response()->json([
           'message'=> 'OTP has been generated successfully.'
        ]);
    }

    public function verify_code(Request $request){
        $otp = Otp::where([
            'otp'=> $request->otp,
            'used'=> 0,
            'usage'=> 1, // Reset Password
            'email'=> $request->email
        ])->firstOrFail();

        $otp->used = 1;
        $otp->save();

        return response()->json([
            'message'=> 'OTP has been verified successfully.'
        ]);
    }

    public function reset_password(Request $request){
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message'=> 'Password has been changed successfully.'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}

