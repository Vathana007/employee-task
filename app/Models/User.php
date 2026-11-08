<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // Without this trait(HasApiTokens), users cannot create tokens.
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_HEAD = 'head';
    public const ROLE_SENIOR = 'senior';
    public const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roleName(): string
    {
        return strtolower((string) $this->role ?: self::ROLE_USER);
    }

    public function hasRole(string ...$roles): bool
    {
        $normalizedRoles = array_map(fn (string $role) => strtolower($role), $roles);

        return in_array($this->roleName(), $normalizedRoles, true);
    }

    public function hasPermission(string $permission): bool
    {
        $permissions = $this->rolePermissions();

        return in_array($permission, $permissions, true);
    }

    public function rolePermissions(): array
    {
        $role = $this->roleRecord()->first();

        if ($role && is_array($role->permissions)) {
            return $role->permissions;
        }

        return Role::defaultPermissionsFor($this->roleName());
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    // Table Relationships
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function roleRecord()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }
}
