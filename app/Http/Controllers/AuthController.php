<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private function logActivity($type, $message, $request, $userId = null, $status = 'active')
    {
        ErrorLog::create([
            'error_type' => $type,
            'message' => $message,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $userId,
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'status' => $status,
        ]);
    }

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

            $this->logActivity(
                ErrorLog::TYPE_REGISTRATION,
                "New user registered: {$user->email}",
                $request,
                $user->id,
                'active'
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            $this->logActivity(
                ErrorLog::TYPE_REGISTRATION_FAILED,
                "Registration failed: Validation error",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Registration exception: {$e->getMessage()}",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Registration failed',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $this->logActivity(
            ErrorLog::TYPE_LOGIN_ATTEMPT,
            "Login attempt for email: {$request->email}",
            $request,
            null
        );

        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($credentials)) {
                $this->logActivity(
                    ErrorLog::TYPE_LOGIN_FAILED,
                    "Failed login: Invalid credentials for email {$request->email}",
                    $request,
                    null,
                    'active'
                );

                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            /** @var User $user */
            $user = Auth::user();

            $this->logActivity(
                ErrorLog::TYPE_LOGIN_SUCCESS,
                "Successful login: User {$user->name} ({$user->email})",
                $request,
                $user->id
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            // Store user role in response
            return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Login exception: {$e->getMessage()}",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Login failed'
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $this->logActivity(
                ErrorLog::TYPE_PROFILE_ACCESS,
                "Profile accessed by user: {$request->user()->name}",
                $request,
                $request->user()->id
            );

            return response()->json([
                'user' => $request->user()
            ]);
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Profile access error: {$e->getMessage()}",
                $request,
                $request->user()?->id,
                'active'
            );

            return response()->json([
                'message' => 'Failed to fetch profile'
            ], 500);
        }
    }

    public function sendVerificationEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $this->logActivity(
                    ErrorLog::TYPE_VERIFICATION_FAILED,
                    "Verification email sent to non-existent user: {$request->email}",
                    $request,
                    null,
                    'active'
                );

                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            if ($user->email_verified_at) {
                $this->logActivity(
                    ErrorLog::TYPE_VERIFICATION_ATTEMPT,
                    "Verification email requested for already verified email: {$user->email}",
                    $request,
                    $user->id
                );

                return response()->json([
                    'message' => 'Email already verified'
                ], 200);
            }

            $verificationToken = Str::random(60);

            DB::table('email_verifications')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($verificationToken),
                    'created_at' => now()
                ]
            );

            $verificationUrl = config('app.frontend_url') . '/verify-email?email=' . urlencode($user->email) . '&token=' . $verificationToken;

            try {
                Mail::send('emails.verify-email', [
                    'user' => $user,
                    'verificationUrl' => $verificationUrl
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Verify Your Email Address');
                });

                $this->logActivity(
                    ErrorLog::TYPE_VERIFICATION_SENT,
                    "Verification email sent to: {$user->email}",
                    $request,
                    $user->id
                );

                return response()->json([
                    'message' => 'Verification email sent. Please check your inbox.',
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                $this->logActivity(
                    ErrorLog::TYPE_EMAIL_ERROR,
                    "Failed to send verification email to {$user->email}: {$e->getMessage()}",
                    $request,
                    $user->id,
                    'active'
                );

                return response()->json([
                    'message' => 'Failed to send verification email'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Send verification email exception: {$e->getMessage()}",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Failed to send verification email'
            ], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string'
            ]);

            $verificationRecord = DB::table('email_verifications')
                ->where('email', $request->email)
                ->first();

            if (!$verificationRecord || !Hash::check($request->token, $verificationRecord->token)) {
                $this->logActivity(
                    ErrorLog::TYPE_VERIFICATION_FAILED,
                    "Invalid verification token for email: {$request->email}",
                    $request,
                    null,
                    'active'
                );

                return response()->json([
                    'message' => 'Invalid or expired verification link'
                ], 422);
            }

            if (now()->diffInHours($verificationRecord->created_at) > 24) {
                DB::table('email_verifications')->where('email', $request->email)->delete();

                $this->logActivity(
                    ErrorLog::TYPE_VERIFICATION_EXPIRED,
                    "Verification token expired for email: {$request->email}",
                    $request,
                    null,
                    'active'
                );

                return response()->json([
                    'message' => 'Verification link expired. Request a new one.'
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            $user->update(['email_verified_at' => now()]);

            DB::table('email_verifications')->where('email', $request->email)->delete();

            $this->logActivity(
                ErrorLog::TYPE_VERIFICATION_SUCCESS,
                "Email verified successfully for user: {$user->email}",
                $request,
                $user->id
            );

            return response()->json([
                'message' => 'Email verified successfully! You can now login.'
            ]);
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Verify email exception: {$e->getMessage()}",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Email verification failed'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            $this->logActivity(
                ErrorLog::TYPE_LOGOUT,
                "User logged out: {$user->name} ({$user->email})",
                $request,
                $user->id
            );

            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Logout exception: {$e->getMessage()}",
                $request,
                $request->user()?->id,
                'active'
            );

            return response()->json([
                'message' => 'Logout failed'
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $this->logActivity(
                    ErrorLog::TYPE_PASSWORD_RESET_FAILED,
                    "Password reset requested for non-existent user: {$request->email}",
                    $request,
                    null,
                    'active'
                );

                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $resetToken = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($resetToken),
                    'created_at' => now()
                ]
            );

            $resetUrl = config('app.frontend_url') . '/reset-password?email=' . urlencode($user->email) . '&token=' . $resetToken;

            try {
                Mail::send('emails.reset-password', [
                    'user' => $user,
                    'resetUrl' => $resetUrl,
                    'resetToken' => $resetToken
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Reset Your Password');
                });

                $this->logActivity(
                    ErrorLog::TYPE_PASSWORD_RESET_REQUEST,
                    "Password reset email sent to: {$user->email}",
                    $request,
                    $user->id
                );

                return response()->json([
                    'message' => 'Password reset link has been sent to your email.',
                ]);
            } catch (\Exception $e) {
                $this->logActivity(
                    ErrorLog::TYPE_EMAIL_ERROR,
                    "Failed to send password reset email to {$user->email}: {$e->getMessage()}",
                    $request,
                    $user->id,
                    'active'
                );

                return response()->json([
                    'message' => 'Failed to send reset email. Please try again.'
                ], 500);
            }
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Forgot password exception: {$e->getMessage()}",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Failed to process password reset'
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string',
                'password' => 'required|min:8|confirmed',
            ]);

            $passwordReset = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
                $this->logActivity(
                    ErrorLog::TYPE_PASSWORD_RESET_FAILED,
                    "Invalid password reset token for email: {$request->email}",
                    $request,
                    null,
                    'active'
                );

                return response()->json([
                    'message' => 'Invalid or expired reset token'
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            $user->update(['password' => Hash::make($request->password)]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            $this->logActivity(
                ErrorLog::TYPE_PASSWORD_RESET_SUCCESS,
                "Password reset successfully for user: {$user->email}",
                $request,
                $user->id
            );

            return response()->json([
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            $this->logActivity(
                ErrorLog::TYPE_SYSTEM_ERROR,
                "Reset password exception: {$e->getMessage()}",
                $request,
                null,
                'active'
            );

            return response()->json([
                'message' => 'Password reset failed'
            ], 500);
        }
    }
}
