<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'username',
        'role_id',
        'treatment_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions()
    {
        return $this->role->permissions;
    }

    public function hasRole(string $role): bool
    {
        return $this->role->name === $role;
    }

    public function hasPermission(string $table, string $permission): bool
    {
        return $this->role->permissions->contains(function ($value, $key) use ($table, $permission) {
            return $value->table_name === $table && $value->name === $permission;
        });
    }

    public function medicalCenters(): BelongsToMany
    {
        return $this->belongsToMany(MedicalCenter::class, 'medical_center_user');
    }

    public function doctorSchedule()
    {
        return $this->hasMany(DoctorSchedule::class, 'user_id');
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }


}
