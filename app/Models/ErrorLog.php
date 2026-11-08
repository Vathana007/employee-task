<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'error_type',
        'message',
        'stack_trace',
        'ip_address',
        'user_agent',
        'user_id',
        'method',
        'endpoint',
        'attempt_count',
        'status',
    ];

    // Error Types
    const TYPE_LOGIN_ATTEMPT = 'login_attempt';
    const TYPE_LOGIN_SUCCESS = 'login_success';
    const TYPE_LOGIN_FAILED = 'login_failed';
    const TYPE_LOGOUT = 'logout';
    const TYPE_REGISTRATION = 'registration';
    const TYPE_REGISTRATION_FAILED = 'registration_failed';
    const TYPE_PROFILE_ACCESS = 'profile_access';
    const TYPE_VERIFICATION_ATTEMPT = 'verification_attempt';
    const TYPE_VERIFICATION_SENT = 'verification_sent';
    const TYPE_VERIFICATION_SUCCESS = 'verification_success';
    const TYPE_VERIFICATION_FAILED = 'verification_failed';
    const TYPE_VERIFICATION_EXPIRED = 'verification_expired';
    const TYPE_PASSWORD_RESET_REQUEST = 'password_reset_request';
    const TYPE_PASSWORD_RESET_SUCCESS = 'password_reset_success';
    const TYPE_PASSWORD_RESET_FAILED = 'password_reset_failed';
    const TYPE_EMAIL_ERROR = 'email_error';
    const TYPE_API_ERROR = 'api_error';
    const TYPE_SYSTEM_ERROR = 'system_error';
    const TYPE_UNAUTHORIZED = 'unauthorized';

    public static function getByType($type)
    {
        return self::where('error_type', $type)->orderBy('created_at', 'desc')->get();
    }

    public static function getActiveErrors()
    {
        return self::where('status', 'active')->orderBy('created_at', 'desc')->paginate(20);
    }

    public static function getByIp($ip)
    {
        return self::where('ip_address', $ip)->orderBy('created_at', 'desc')->get();
    }

    public static function getByUser($userId)
    {
        return self::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    public function resolve()
    {
        $this->update(['status' => 'resolved']);
        return true;
    }

    public static function getSummary()
    {
        return [
            'total_errors' => self::count(),
            'active_errors' => self::where('status', 'active')->count(),
            'login_attempts' => self::where('error_type', self::TYPE_LOGIN_ATTEMPT)->count(),
            'login_success' => self::where('error_type', self::TYPE_LOGIN_SUCCESS)->count(),
            'login_failed' => self::where('error_type', self::TYPE_LOGIN_FAILED)->count(),
            'registrations' => self::where('error_type', self::TYPE_REGISTRATION)->count(),
            'logouts' => self::where('error_type', self::TYPE_LOGOUT)->count(),
            'email_verifications' => self::where('error_type', self::TYPE_VERIFICATION_SUCCESS)->count(),
            'password_resets' => self::where('error_type', self::TYPE_PASSWORD_RESET_SUCCESS)->count(),
            'unique_ips' => self::distinct()->count('ip_address'),
            'unique_users' => self::whereNotNull('user_id')->distinct()->count('user_id'),
        ];
    }
}
