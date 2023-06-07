<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Account already Activated Successfully !',
            ], 200);;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);
        $tokenData = DB::table('password_resets')
            ->where('email', $user->email)
            ->first();
        $token = $tokenData->token;
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Account Activated Successfully !',
                'CreatePasswordToken' => $token
            ], 200);
        }
    }
    
}
