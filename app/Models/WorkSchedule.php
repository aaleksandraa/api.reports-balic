<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'work_schedule';

    protected $fillable = [
        'report_id',
        'employee_id',
        'employee_name',
        'arrival_time',
        'departure_time',
        'hours_worked',
        'status',
        'notes',
    ];

    protected $casts = [
        'hours_worked' => 'float',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
