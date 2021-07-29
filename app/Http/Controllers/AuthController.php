<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller {
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
            'success' => true
        ];
    }

    public function user(Request $request) {
        return $request->user()->with('Store')->firstOrFail();
    }
}