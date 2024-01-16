<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $reg = new User($request->all());
        try {
            $reg->save();
        } catch (\Throwable $th) {
            return ["message" => "error", "error.message" => $th->getMessage()];
        }
        return $reg;
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('Personal Access Token')->accessToken;

            return response()->json(['access_token' => $token], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
