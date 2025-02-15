<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{ 
        

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);
        try {
            $user=User::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'User created successfully',
                'data' => $user
            ],201);}
        catch (\Exception $e) {
            return response()->json(['error' => 'فشل انشاء المهمة', 'message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = User::where('email',$request->email)->first();
        $token = $user->createToken('auth_token',)->plainTextToken;
        return response()->json(['message' => 'User logged in successfully', 'token' => $token,'user'=>$user], 200);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'User logged out successfully'], 200);
    }
      

   
   

    
}
