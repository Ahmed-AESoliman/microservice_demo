<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function login(Request $request)
    {
        $data = $request->only('email', 'password');
        $response = $this->userService->post('login', $data);
        $cookie = cookie('token',$response['data']['accessToken']); 
        return response($response)->withCookie($cookie);
    }
    public function user(Request $request)
    {
        $user = $this->userService->get('user');
        
        return $user;
    }
}
