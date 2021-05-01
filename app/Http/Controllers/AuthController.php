<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
    public function register(Request $request) {
        $request->validate([
            'phone_number' => 'required|string|unique:users',
            'name' => 'required|string',
            'password' => 'required|string|confirmed',
            'store_id' => 'required|integer|exists:stores,id'
        ]);
        
        $user = new User([
            'phone_number' => $request->phone_number,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'store_id' => $request->store_id
        ]);
        
        $user->save();
        
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    public function login(Request $request){
        $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);        
        
        $credentials = request(['phone_number', 'password']);        
        
        if (!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
            
        $user = $request->user();        
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addDays(1);
        
        $token->save();
        
        return [
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        
        return [
            'message' => 'Successfully logged out'
        ];
    }

    public function user(Request $request) {
        return $request->user()->with('store')->firstOrFail();
    }
}