<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TodayPatientQuick extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'today_patients_quick';

    protected $fillable = [
        'report_id',
        'service_id',
        'service_name',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
