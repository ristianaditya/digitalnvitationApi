<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function sendResetLink(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'The email you provided is not yet registered.'], 422);
        }

        $email = $request->input('email');
        $token = Str::random(60);

        // Store the token in the database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send the email
        Mail::to($email)->send(new PasswordResetMail($token, $email));

        return response()->json(['message' => 'Password reset link sent!']);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $email = $request->input('email');
        $token = $request->input('token');
        $password = $request->input('password');

        // Verify the token
        $passwordReset = DB::table('password_resets')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'This password reset token is invalid.'], 400);
        }

        // Update the user's password
        DB::table('users')
            ->where('email', $email)
            ->update(['password' => bcrypt($password)]);

        // Delete the token
        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json(['message' => 'Password has been successfully reset!']);
    }
}