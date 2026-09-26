<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        // if (!$user->email_verified_at) {
        //     return response()->json([
        //         'message' => 'Please verify your email first. Check your inbox for verification link.',
        //         'email' => $user->email,
        //     ], 403);
        // }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function sendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // If already verified, no need to send again
        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified'
            ], 200);
        }

        // Generate verification token
        $verificationToken = Str::random(60);

        // Store in database
        DB::table('email_verifications')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($verificationToken),
                'created_at' => now()
            ]
        );

        // Build verification URL
        $verificationUrl = config('app.frontend_url') . '/verify-email?email=' . urlencode($user->email) . '&token=' . $verificationToken;

        // Send email
        try {
            Mail::send('emails.verify-email', [
                'user' => $user,
                'verificationUrl' => $verificationUrl
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Verify Your Email Address');
            });

            return response()->json([
                'message' => 'Verification email sent. Please check your inbox.',
                'email' => $user->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send verification email'
            ], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string'
        ]);

        $verificationRecord = DB::table('email_verifications')
            ->where('email', $request->email)
            ->first();

        if (!$verificationRecord || !Hash::check($request->token, $verificationRecord->token)) {
            return response()->json([
                'message' => 'Invalid or expired verification link'
            ], 422);
        }

        // Check if token expired (24 hours)
        if (now()->diffInHours($verificationRecord->created_at) > 24) {
            DB::table('email_verifications')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'Verification link expired. Request a new one.'
            ], 422);
        }

        // Mark email as verified
        $user = User::where('email', $request->email)->first();
        $user->update(['email_verified_at' => now()]);

        // Delete used token
        DB::table('email_verifications')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Email verified successfully! You can now login.'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Generate reset token
        $resetToken = Str::random(60);

        // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($resetToken),
                'created_at' => now()
            ]
        );

        // Build reset URL
        $resetUrl = config('app.frontend_url') . '/reset-password?email=' . urlencode($user->email) . '&token=' . $resetToken;

        // Send email with reset link
        try {
            Mail::send('emails.reset-password', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'resetToken' => $resetToken
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Reset Your Password');
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send reset email. Please try again.'
            ], 500);
        }

        return response()->json([
            'message' => 'Password reset link has been sent to your email.',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'Invalid or expired reset token'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Delete the used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }
}
