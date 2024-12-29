<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrintedInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_ids',
        'created_by'
    ];

    protected $casts = [
        'appointment_ids' => 'array'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class, 'printed_invoice_appointment');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
