<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedProcedure extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_id',
        'patient_first_name',
        'patient_last_name',
        'procedure_type',
        'procedure_details',
        'planned_date',
        'planned_month',
        'notes',
    ];

    protected $casts = [
        'planned_date' => 'date',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }

    // Helper accessor za puno ime
    public function getFullNameAttribute(): string
    {
        return trim($this->patient_first_name . ' ' . $this->patient_last_name);
    }

    // Helper accessor za planirani period
    public function getPlannedPeriodAttribute(): string
    {
        if ($this->planned_date) {
            return $this->planned_date->format('d.m.Y');
        }

        return $this->planned_month ?? '-';
    }
}
