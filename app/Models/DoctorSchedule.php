<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'medical_center_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];
    protected $hidden = ['created_at', 'updated_at'];

}
