<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        // Validate the input email
        $request->validate(['email' => 'required|email']);
    
        $email = $request->email;
        $currentTimestamp = Carbon::now();
    
        // Fetch the OTP record for the given email
        $otpRecord = Otp::where('email', $email)->first();
    
        if ($otpRecord) {
            // Check if 2 hours have passed since the last attempt
            if ($otpRecord->last_attempt_at && $otpRecord->last_attempt_at->diffInHours($currentTimestamp) >= 2) {
                // Reset attempts if the 2-hour window has passed
                $otpRecord->attempts = 0;
            }
    
            // Check if the maximum attempts have been reached
            if ($otpRecord->attempts >= 5) {
                return response()->json([
                    'message' => 'Maximum OTP attempts reached. Try again later.',
                    'next_attempt' => $otpRecord->last_attempt_at->addHours(2)->format('Y-m-d H:i:s')
                ], 429);
            }
    
            // Increment the attempts count
            $otpRecord->increment('attempts');
    
            // Update OTP and expiration time
            $otpRecord->update([
                'otp' => random_int(100000, 999999),
                'expires_at' => $currentTimestamp->addMinutes(5),
                'last_attempt_at' => $currentTimestamp
            ]);
        } else {
            // Create a new record if it doesn't exist
            $otpRecord = Otp::create([
                'email' => $email,
                'otp' => random_int(100000, 999999),
                'expires_at' => $currentTimestamp->addMinutes(5),
                'attempts' => 1,
                'last_attempt_at' => $currentTimestamp,
            ]);
        }
    
        // Send the email with the OTP
        Mail::to($email)->send(new OtpMail($otpRecord->otp, $email));
    
        return response()->json([
            'message' => 'OTP sent successfully to ' . $email,
            'expires_at' => $otpRecord->expires_at->format('Y-m-d H:i:s'),
            'attempts_remaining' => 5 - $otpRecord->attempts
        ]);
    }
    
    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string'
        ]);

        // Find the OTP record
        $otpRecord = Otp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid OTP or email.'], 400);
        }

        // Check if OTP is expired
        if (Carbon::now()->isAfter($otpRecord->expires_at)) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        // Find the user associated with the email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Update the user's verified status
        $user->update(['verified' => true]);

        // OTP verified
        $otpRecord->delete();

        return response()->json(['message' => 'OTP verified successfully.'], 200);
    }
}
