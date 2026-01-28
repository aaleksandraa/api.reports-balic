<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'role',
        'job_title',
        'active',
        'daily_work_hours',
        'weekly_work_hours',
        'monthly_work_hours',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'active' => 'boolean',
        'daily_work_hours' => 'decimal:2',
        'weekly_work_hours' => 'decimal:2',
        'monthly_work_hours' => 'decimal:2',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    // Relationships
    public function locations()
    {
        return $this->belongsToMany(Location::class, 'staff_locations');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
