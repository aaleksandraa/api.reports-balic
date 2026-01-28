<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'email',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_locations');
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'staff_locations');
    }
}
