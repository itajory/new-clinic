<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrescriptionTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prescription_templates';

    protected $fillable = ['name', 'content', 'treatment_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }
}
