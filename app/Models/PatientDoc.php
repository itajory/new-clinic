<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class PatientDoc extends Model
{
    use HasFactory;

//        , SoftDeletes;

    protected $fillable = ['title', 'path', 'parent_id', 'patient_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    // get all children of this folder in the first level only
    public function children(): HasMany
    {
        return $this->hasMany(PatientDoc::class, 'parent_id', 'id');
    }

    // get parent folder
    public function parent(): BelongsTo
    {
        return $this->belongsTo(PatientDoc::class, 'parent_id', 'id');
    }

    // get all parents of this folder parent chain
    public function parents()
    {
        return $this->parent ? $this->parent->parents()->merge($this->parent) : collect();
    }

    public function parentsArray(): array
    {
        $parents = [];
        $current = $this->parent;

        while ($current) {
            $parents[] = $current;
            $current = $current->parent;
        }
        return array_reverse($parents);
    }

}
