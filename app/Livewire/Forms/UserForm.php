<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;
    public $name;
    public $email;
    public $phone;
    public $username;
    public $password;
    public int $role_id = 0;
    public $treatment_id;
    public array $medicalCenters = [];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                'unique:users,email,'.($this->user?->id ?? 'NULL'),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                'unique:users,phone,'.($this->user?->id ?? 'NULL'),
            ],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($this->user?->id),
            ],
            'password' => [$this->user ? 'nullable' : 'required', 'string', 'max:255'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'treatment_id' => [
                'required_if:role_id,2', // doctor
                'integer',
                'nullable',
                'exists:treatments,id',
            ],
            'medicalCenters' => ['required', 'array'],
            'medicalCenters.*' => ['required', 'integer', 'exists:medical_centers,id'],
        ];
    }

    public function store()
    {
        // dd($this->rules());
        $this->validate();
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'username' => $this->username,
            'password' => bcrypt($this->password),
            'role_id' => $this->role_id,
            'treatment_id' => $this->treatment_id,
        ]);
        if ($user) {
            $user->medicalCenters()->attach($this->medicalCenters);
        }

        //        $user->medicalCenters()->attach(array_keys($this->medicalCenters));
        return $user;
    }

    public function update(): void
    {
        $this->validate();
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'username' => $this->username,
            'role_id' => $this->role_id,
            'treatment_id' => $this->treatment_id,
        ]);
        $this->user->medicalCenters()->sync($this->medicalCenters);
    }

    public function changePassword(): void
    {
        $this->validate([
            'password' => ['required', 'string', 'max:255'],
        ]);
        $this->user->update([
            'password' => bcrypt($this->password),
        ]);
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->username = $user->username;
        $this->role_id = $user->role_id;
        $this->treatment_id = $user->treatment_id;
        if ($user->medicalCenters()->count() > 0) {
            $this->medicalCenters = $user->medicalCenters
                ->map(function ($medicalCenter) {
                    return $medicalCenter->id;
                })->toArray();
        }
    }
}
