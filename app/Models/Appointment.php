<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'doctor_id',
        'medical_center_id',
        'treatment_id',
        'appointment_time',
        'status',
        'duration',
        'patient_id',
        'created_by',
        'note',
        'repeat',
        'price',
        'discount',
        'patient_fund_id',
        'patient_fund_amount',
        'patient_fund_contribution_type',
        'patient_fund_total',
        'total',
        'repeat_id',
        'is_patient_fund_closed',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicalCenter(): BelongsTo
    {
        return $this->belongsTo(MedicalCenter::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function patientFund(): BelongsTo
    {
        return $this->belongsTo(PatientFund::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class, 'appointment_payment');
    }

    public function scopeNotPaidAndCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'not_attended_with_telling', 'not_attended_without_telling'])
            ->where('total', '>', 0)
            ->whereDoesntHave('payments');
    }
}
