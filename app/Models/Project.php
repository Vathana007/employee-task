<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
    ];

    // Table Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function syncStatus(): void
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return;
        }

        $completed = $this->tasks()->where('status', 'completed')->count();
        $started = $this->tasks()->whereIn('status', ['in_progress', 'completed'])->count();

        $status = $completed === $total ? 'completed' : ($started > 0 ? 'in_progress' : 'pending');

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
