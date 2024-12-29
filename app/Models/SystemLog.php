<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medical_center_id',
        'details',
        'table_id',
        'event_type',
        'event_description',
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function getModelNameAttribute()
    {
        return class_basename($this->model_type);
    }


    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'table_id', 'id')
            ->when(fn($query) => str_contains($this->event_type, 'Appointment'), function ($query) {
                return $query;
            });
    }


    public function medicalCenter()
    {
        return $this->belongsTo(MedicalCenter::class, 'table_id', 'id')
            ->when(fn($query) => str_contains($this->event_type, 'MedicalCenter'), function ($query) {
                return $query;
            });
    }
}
