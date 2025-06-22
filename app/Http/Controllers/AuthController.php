<?php

namespace App\Http\Controllers;

use App\Mail\VerificationMail;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Generate verification code
        $verificationCode = rand(100000, 999999);

        // 3. Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'verification_code' => $verificationCode,
        ]);


        // Mail::to($user->email)->send(new VerificationMail());
        return response()->json([
            'message' => 'Account created successfully. Please check your email for the verification code.'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        // if (is_null($user->email_verified_at)) {
        //     return response()->json(['message' => 'Email not verified.'], 403);
        // }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->verification_code != $request->verification_code) {
            return response()->json(['message' => 'Invalid verification code.'], 400);
        }
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->save();
        return response()->json(['message' => 'Email verified successfully.']);
    }
    public function resendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Email already verified.'], 400);
        }

        $verificationCode = rand(100000, 999999);
        $user->verification_code = $verificationCode;
        $user->save();

        Mail::raw("Your new verification code is: {$verificationCode}", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Resend Verification Code');
        });

        return response()->json(['message' => 'Verification code resent.']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 500);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successfully.'])
            : response()->json(['message' => 'Password reset failed.'], 500);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
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

    public function destroy($id)
    {
        $user = User::where('id', $id)->first();
        $user->delete();
        return response()->json([
            'message' => "seccessfully"
        ], 200);
    }
}
