<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Check extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bank_id',
        'account_number',
        'check_number',
        'amount',
        'date',
        'status',
        'created_by',
        'payment_id',
        'replaced_by' // check
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function replacement(): BelongsTo
    {
        return $this->belongsTo(Check::class, 'replaced_by');
    }
}
