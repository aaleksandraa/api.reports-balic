<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyReport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'location_id',
        'date',
        'day_of_week',
        'notes',
        'submitted_by',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function fiscalItems(): HasMany
    {
        return $this->hasMany(FiscalItem::class, 'report_id');
    }

    public function nonFiscalItems(): HasMany
    {
        return $this->hasMany(NonFiscalItem::class, 'report_id');
    }

    public function cardPayments(): HasMany
    {
        return $this->hasMany(CardPayment::class, 'report_id');
    }

    public function wireTransfers(): HasMany
    {
        return $this->hasMany(WireTransfer::class, 'report_id');
    }

    public function associates(): HasMany
    {
        return $this->hasMany(Associate::class, 'report_id');
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'report_id');
    }

    public function workSchedule(): HasMany
    {
        return $this->hasMany(WorkSchedule::class, 'report_id');
    }

    public function unpaidExams(): HasMany
    {
        return $this->hasMany(UnpaidExam::class, 'report_id');
    }

    public function todayPatientsQuick(): HasMany
    {
        return $this->hasMany(TodayPatientQuick::class, 'report_id');
    }

    public function todayPatientsDetailed(): HasMany
    {
        return $this->hasMany(TodayPatientDetailed::class, 'report_id');
    }

    public function plannedProcedures(): HasMany
    {
        return $this->hasMany(PlannedProcedure::class, 'report_id');
    }
}
