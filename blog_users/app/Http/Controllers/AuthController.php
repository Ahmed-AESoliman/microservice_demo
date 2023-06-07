<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthenticatedUserResource;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $userData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
            'img'=>'nullable'
        ]);
        $user = User::create($userData);
        $user->notify(new VerifyEmailNotification());
        return new AuthenticatedUserResource($user);
    }
    public function login(Request $request)
    {
        if(!auth()->check()){
            $credentials = $request->validate([
                'email' => 'required|email|string',
                'password' => [
                    'required',
                ],
                'remember_token' => 'boolean',
            ]);
            $remember = $credentials["remeber"] ?? false;
            unset($credentials["remember_token"]);
            if (!Auth::attempt($credentials, $remember)) {
                return response([
                    'error' => [
                        'msg' =>'The provided credentials are not correct',
                    ]
                ], 422);
            }
            $user = Auth::user();
            if(!$user->hasVerifiedEmail()){
                return response([
                    'error' => [
                        'msg' =>'Your email address is not verified.',
                    ]
                ], 403);
            }
            return new AuthenticatedUserResource($user);
        }
        return redirect('/');
    }
    public function user(Request $request)
    {
        return 'sss';
    }
}
