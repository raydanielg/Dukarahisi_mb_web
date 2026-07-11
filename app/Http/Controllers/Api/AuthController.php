<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user with phone number.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $otp = SmsService::generateOtp(6);

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(5),
        ]);

        SmsService::sendOtp($user->phone_number, $otp);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered. Please verify your phone number with the OTP sent.',
            'data' => [
                'user_id' => $user->id,
                'phone_number' => $user->phone_number,
            ],
        ], 201);
    }

    /**
     * Verify OTP and activate account.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || $user->otp_code !== $request->otp_code) {
            throw ValidationException::withMessages(['otp_code' => 'Invalid OTP code.']);
        }

        if ($user->otp_expires_at && $user->otp_expires_at->isPast()) {
            throw ValidationException::withMessages(['otp_code' => 'OTP has expired.']);
        }

        $user->phone_verified_at = Carbon::now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Phone number verified successfully.',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ]);
    }

    /**
     * Login user with phone number and password.
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['phone_number' => 'Invalid phone number or password.']);
        }

        if (!$user->hasVerifiedPhone()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number not verified.',
                'data' => [
                    'phone_verified' => false,
                    'phone_number' => $user->phone_number,
                ],
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
        ]);
    }

    /**
     * Request password reset via OTP.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number not found.',
            ], 404);
        }

        $otp = SmsService::generateOtp(6);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        SmsService::sendOtp($user->phone_number, $otp);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset OTP sent to your phone number.',
        ]);
    }

    /**
     * Reset password using OTP.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'otp_code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('phone_number', $request->phone_number)
            ->where('otp_code', $request->otp_code)
            ->first();

        if (!$user || ($user->otp_expires_at && $user->otp_expires_at->isPast())) {
            throw ValidationException::withMessages(['otp_code' => 'Invalid or expired OTP.']);
        }

        $user->password = Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully.',
        ]);
    }

    /**
     * Get authenticated user.
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ]);
    }
}
