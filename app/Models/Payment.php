<?php

namespace App\Models;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_type',
        'amount',
        'attachment',
        // 'date', there is no need
        'patient_id',
        'created_by',
        'check_id',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'appointment_payment');
    }

    public function check(): HasOne
    {
        return $this->hasone(Check::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
