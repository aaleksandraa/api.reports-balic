<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardPayment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'report_id',
        'service_name',
        'price',
        'doctor_counts',
    ];

    protected $casts = [
        'price' => 'float',
        'doctor_counts' => 'array',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(DailyReport::class, 'report_id');
    }
}
