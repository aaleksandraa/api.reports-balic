<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Associate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_id',
        'service_name',
        'doctor_id',
        'count',
        'price',
        'type',
    ];

    protected $casts = [
        'price' => 'float',
        'count' => 'integer',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
