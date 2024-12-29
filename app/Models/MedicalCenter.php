<?php

namespace App\Models;

use App\Models\City;
use App\Models\WorkingHour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalCenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'phone', 'fax', 'whatsapp', 'email', 'city_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'medical_center_user')
            ->whereHas('role', function ($query) {
                $query->where('name', 'doctor');
            });
    }
}
