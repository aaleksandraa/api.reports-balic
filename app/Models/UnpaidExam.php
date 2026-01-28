<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnpaidExam extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_id',
        'patient_first_name',
        'patient_last_name',
        'reason',
        'doctor_id',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    // Helper accessor za puno ime
    public function getFullNameAttribute(): string
    {
        return trim($this->patient_first_name . ' ' . $this->patient_last_name);
    }
}
