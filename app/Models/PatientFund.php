<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PatientFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_funds';

    protected $fillable = ['name', 'contribution_type'];
    protected $hidden = ['created_at', 'updated_at'];

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_fund_associations')->withPivot('contribution_percentage');
    }


    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
