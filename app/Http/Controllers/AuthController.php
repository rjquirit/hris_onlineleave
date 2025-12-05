<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email using blind index search
        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($request->email);

        $user = User::where('email_search_index', $emailSearchIndex)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login details',
            ], 401);
        }

        // Log the user in
        Auth::login($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if email already exists using blind index
        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($validatedData['email']);

        $existingUser = User::where('email_search_index', $emailSearchIndex)->first();

        if ($existingUser) {
            return response()->json([
                'message' => 'The email has already been taken.',
                'errors' => ['email' => ['The email has already been taken.']],
            ], 422);
        }

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'message' => 'Registration successful',
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    // Password Reset Request
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($request->email);

        $user = User::where('email_search_index', $emailSearchIndex)->first();

        if (! $user) {
            // Return success message even if user doesn't exist (security best practice)
            return response()->json([
                'message' => 'If that email exists in our system, a password reset link has been sent.',
            ], 200);
        }

        // Generate reset token
        $token = Str::random(60);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // TODO: Send email with reset link
        // For now, return token in response (remove in production)
        // Mail::to($user)->send(new PasswordResetMail($token));

        return response()->json([
            'message' => 'Password reset link sent to your email.',
            'reset_token' => $token, // TODO: Remove this in production
            'reset_url' => url("/reset-password?token={$token}&email=".urlencode($request->email)),
        ], 200);
    }

    // Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($request->email);

        $user = User::where('email_search_index', $emailSearchIndex)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Invalid reset token or email.',
            ], 400);
        }

        // Verify token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->first();

        if (! $resetRecord || ! Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'message' => 'Invalid or expired reset token.',
            ], 400);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            return response()->json([
                'message' => 'Reset token has expired. Please request a new one.',
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Generate new auth token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Password reset successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    // Send Email Verification
    public function sendEmailVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        // TODO: Send verification email
        // $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent.',
        ], 200);
    }

    // Verify Email
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->email))) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 200);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'Email verified successfully.',
        ], 200);
    }

    // Generate OTP
    public function generateOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($request->email);

        $user = User::where('email_search_index', $emailSearchIndex)->first();

        if (! $user) {
            return response()->json([
                'message' => 'If that email exists, an OTP has been sent.',
            ], 200);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 5 minutes
        $cacheKey = 'otp:'.$request->email;
        Cache::put($cacheKey, [
            'code' => $otp,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5)->timestamp,
        ], 300); // 5 minutes

        // TODO: Send OTP via email
        // Mail::to($user)->send(new OTPMail($otp));

        return response()->json([
            'message' => 'OTP sent to your email.',
            'otp' => $otp, // TODO: Remove this in production
            'expires_in' => 300, // seconds
        ], 200);
    }

    // Verify OTP
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $cacheKey = 'otp:'.$request->email;
        $otpData = Cache::get($cacheKey);

        if (! $otpData) {
            return response()->json([
                'message' => 'OTP has expired or is invalid.',
            ], 400);
        }

        // Check max attempts
        if ($otpData['attempts'] >= 3) {
            Cache::forget($cacheKey);

            return response()->json([
                'message' => 'Maximum OTP attempts exceeded. Please request a new OTP.',
            ], 400);
        }

        // Verify OTP
        if ($otpData['code'] !== $request->otp) {
            $otpData['attempts']++;
            Cache::put($cacheKey, $otpData, now()->diffInSeconds($otpData['expires_at']));

            return response()->json([
                'message' => 'Invalid OTP. '.(3 - $otpData['attempts']).' attempts remaining.',
            ], 400);
        }

        // OTP is valid, log user in
        $encryptionService = app(\App\Services\EncryptionService::class);
        $emailSearchIndex = $encryptionService->generateBlindIndex($request->email);

        $user = User::where('email_search_index', $emailSearchIndex)->first();

        if (! $user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        // Clear OTP from cache
        Cache::forget($cacheKey);

        // Generate auth token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 200);
    }

    // Google OAuth Redirect
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless() // For API, don't use sessions
            ->redirect();
    }

    // Google OAuth Callback
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $encryptionService = app(\App\Services\EncryptionService::class);
            $emailSearchIndex = $encryptionService->generateBlindIndex($googleUser->getEmail());

            // Find or create user
            $user = User::where('email_search_index', $emailSearchIndex)->first();

            if (! $user) {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(32)), // Random password
                    'email_verified_at' => now(), // Auto-verify Google emails
                    'google_id' => $googleUser->getId(),
                ]);
            } else {
                // Update Google ID if not set
                if (! $user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->email_verified_at = $user->email_verified_at ?? now();
                    $user->save();
                }
            }

            // Generate Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Redirect to frontend with token
            return redirect(config('tyro-login.redirects.after_login', '/personnel').'?token='.$token);

        } catch (\Exception $e) {
            return redirect('/login?error=google_auth_failed');
        }
    }
}
