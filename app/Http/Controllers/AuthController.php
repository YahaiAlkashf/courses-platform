<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:8'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        }

        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();
            $token = $user->createToken("auth-token")->plainTextToken;
            return response()->json([
                'status' => true,
                'token' => $token,
                'role' => $user->role
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
    public function register(Request $request)
    {

            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password
            ]);
            $token = $user->createToken("auth-token")->plainTextToken;
            return response()->json([
                'status' => true,
                'token' => $token,
                'user' => $user
            ], 201);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function users(Request $request)
    {
        $users = User::all();
        return response()->json([
            'users' => $users
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'logout seccess',
        ]);
    }
    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        $user->delete();
        return response()->json([
            'message' => "seccessfully"
        ], 200);
    }
}
