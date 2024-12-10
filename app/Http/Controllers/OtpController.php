<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Generate a random OTP
        $otp = random_int(100000, 999999);

        // Calculate expiry time
        $expiresAt = Carbon::now()->addMinutes(5);

        // Store or update the OTP in the database
        Otp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
	);

        // Send the email
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP sent successfully to ' . $request->email]);
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

        // OTP verified
        $otpRecord->delete();

        return response()->json(['message' => 'OTP verified successfully.'], 200);
    }
}
