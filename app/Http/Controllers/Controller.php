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
        // ตรวจสอบว่าอีเมลมีอยู่ในระบบหรือไม่
         $existingUser = User::where('email', $request['email'])->first();

        if ($existingUser) {
            // ถ้ามีผู้ใช้อื่นใช้อีเมลนี้แล้ว
            return response()->json(['message' => 'exist'], 400);
        }

        // ถ้าอีเมลยังไม่มีในระบบ ให้ลงทะเบียนผู้ใช้
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'bio'=>$request['bio'],
            'password' => Hash::make($request['password'])
        ]);

        return response()->json(['message' => 'success'], 200);
    }
    public function verifyLogin(Request $request){

        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|'
        ]);
        if(!auth()->attempt($attrs)){
            return response([
                'message' => 'Invalid credentials.',
                'status' => 1
            ], 403);
        }
        // if(!Auth::attempt($request->only('email', 'password'))){
        //     return response(['message' => 'Invalid Credentials','data' => $request],
        //     Response::HTTP_UNAUTHORIZED);
        // }
        return response([
            'user_id' => auth()->user()->id,
            'jwt_token' => auth()->user()->createToken('token')->plainTextToken,
            'message' => 'Login Success',
            'status' => 1
        ],200);
    }

    public function editProfile(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'bio' => 'string|nullable', // bio สามารถเป็นค่าว่างได้
        ]);
        $user = auth()->user();

        $user->name =$request['name'];
        $user->email = $request['email'];
        $user->bio = $request['bio'];
        $user->save();

        // ส่งการตอบกลับสำเร็จ
        return response()->json(['message' => 'success'],200);

    }
    public function changePassword(Request $request){
         // Validate the incoming request data
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required',
        ]);
        $user = auth()->user();


        if(!Hash::check($request->current_password,$user->password)){
            return response()->json(['message' => 'Current password is incorrect'], 401);

        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json(['message' => 'change password success'],200);
    }

    public function user()
    {
        return response()->json(auth()->user());
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();
        return  response(['message' => 'Logout successful'],200);
    }
    public function dropUser(Request $request){
        if(auth()->user()){
            // ลบ token ของผู้ใช้ 
            auth()->user()->tokens()->delete();
    
            // ลบบัญชีผู้ใช้
            auth()->user()->delete();
    
            return response([
                'message' => 'ลบบัญชีผู้ใช้เรียบร้อย.',
            ], 200);
        } else {
            return response([
                'message' => 'ไม่พบผู้ใช้ที่ลงชื่อเข้าใช้.',
            ], 404);
        }
    }
}

