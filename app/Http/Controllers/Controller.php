<?php

namespace App\Http\Controllers;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\User;
use App\Http\Controllers\Cookies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function register(Request $request){


        $user = User::create([
            'name'=> $request['name'],
            'email'=> $request['email'],
            'password'=> Hash::make($request['password'])
        ]);
        return $user;
    }
    public function verifyLogin(Request $request){

        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|'
        ]);
        if(!auth()->attempt($attrs)){
            return response([
                'message' => 'Invalid credentials.',
            ], 403);
        }
        // if(!Auth::attempt($request->only('email', 'password'))){
        //     return response(['message' => 'Invalid Credentials','data' => $request],
        //     Response::HTTP_UNAUTHORIZED);
        // }
        return response([
            'email' => $request->email,
            'user_id' => auth()->user()->id,
            'jwt_token' => auth()->user()->createToken('token')->plainTextToken,
            'message' => 'Login Success',
            'status' => 1
        ],200);
    }

    public function user()
    {
        return response()->json(auth()->user());
    }
    public function logout(Request $request){
        $cookie = \Cookie::forget('jwt'); 
        return  response(['message' => 'Logout successful'])->withCookie($cookie);
    }
}

