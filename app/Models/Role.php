<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public static function defaultPermissionsFor(string $role): array
    {
        return match (strtolower($role)) {
            'admin' => [
                'manage_users',
                'manage_roles',
                'manage_projects',
                'manage_tasks',
                'view_all_projects',
                'delete_any_project',
                'delete_any_task',
                'view_admin_logs',
            ],
            'head' => [
                'manage_projects',
                'manage_tasks',
                'view_all_projects',
                'delete_own_projects',
                'delete_own_tasks',
                'approve_work',
            ],
            'senior' => [
                'manage_tasks',
                'view_all_projects',
                'create_projects',
                'edit_own_projects',
                'delete_own_projects',
                'update_task_status',
            ],
            'user' => [
                'view_assigned_projects',
                'view_assigned_tasks',
                'update_own_task_status',
            ],
            default => [],
        };
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }
}
