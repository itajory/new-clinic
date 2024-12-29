<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'price', 'duration'];
    protected $hidden = ['created_at', 'updated_at'];

    public function prescriptionTemplates(): HasMany
    {
        return $this->hasMany(PrescriptionTemplate::class);
    }
}
