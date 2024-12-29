<?php

namespace App\Models;

use App\Models\Invoice;
use App\Models\Payment;
use App\enums\GenderEnum;
use App\Models\PatientDoc;
use App\Models\Appointment;
use App\Models\PatientFund;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'gender',
        'birth_date',
        'id_number',
        'guardian_phone',
        'patient_phone',
        'city_id',
    ];

    protected $casts = [
        'gender' => GenderEnum::class,
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function patientFunds(): BelongsToMany
    {
        return $this->belongsToMany(
            PatientFund::class,
            'patient_fund_associations'
        )->withPivot('contribution_percentage');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDoc::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(PatientRecord::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(related: Appointment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function checks()
    {
        return $this->hasManyThrough(Check::class, Payment::class)
            ->where('payments.payment_type', 'check');
    }
}
