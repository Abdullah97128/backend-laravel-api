<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if($user){
            $user->register = true;
        }
        return $user;
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($loginData)) {
            return response()->json(['message' => 'Invalid credentials']);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response()->json(['user' => Auth::user(), 'access_token' => $accessToken]);
    }

    public function logout(Request $request,$id)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }    
        return response()->json(['message' => 'Successfully logged out']);
    }
    

    public function user(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function savePreferences(Request $request, $userId)
    {
        $user = User::find($userId);        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->preferences = $request->all();
        $user->save();

        return response()->json(['user' => $user]);
    }
}
