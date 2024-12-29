<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatientRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'treatment_id',
        'doctor_id',
        'appointment_id',
        'medical_center_id',
        'description'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function medicalCenter()
    {
        return $this->belongsTo(MedicalCenter::class);
    }
}
