<?php

namespace App\Livewire\Doctor;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MedicalCenter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class DoctorIndex extends Component
{
    use WithPagination;

    public array $headers;
    public string $searchWord = '';
    public bool $showFilterDrawer = false;

    public int $perPage;
    public array $perPageOptions;
    public array $sortBy;
    public int $medicalCenterId = 0;
    public bool $showArchived = false;
    public Collection $medicalCenters;


    public function mount()
    {
        $this->authorize('viewAny', User::class);
        $this->headers = [
            ['key' => 'id', 'label' => __('id')],
            ['key' => 'name', 'label' => __('name')],
            ['key' => 'email', 'label' => __('email')],
            ['key' => 'username', 'label' => __('username')],
            ['key' => 'medicalCenters', 'label' => __('medical_center'), 'sortable' => false],
        ];
        $this->perPageOptions = [10, 20, 50, 100];
        $this->perPage = $this->perPageOptions[0];
        // $this->medicalCenters = MedicalCenter::all();
        $this->medicalCenters = Cache::remember('medical_centers', 60 * 60, function () {
            return MedicalCenter::all();
        });
        $this->sortBy = ['column' => 'id', 'direction' => 'asc', 'class' => 'text-red-500'];
    }


    public function render()
    {
        return view('livewire.doctor.doctor-index')->layout('layouts.dash');
    }

    public function users()
    {
        $users = User::where(function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                ->orWhereRaw('LOWER(id) LIKE ?', ['%' . strtolower($this->searchWord) . '%']);
        })
            //            ->when($this->roleId > 0, function ($query) {
            //                $query->whereHas('role', function ($query) {
            //                    $query->where('id', $this->roleId);
            //                });
            //            })
            ->when($this->medicalCenterId > 0, function ($query) {
                $query->whereHas('medicalCenters', function ($query) {
                    $query->where('id', $this->medicalCenterId);
                });
            })
            ->when($this->showArchived, function ($query) {
                $query->onlyTrashed();
            })
            ->whereHas('role', function ($query) {
                $query->where('id', 2);
            })
            ->orderBy(...array_values($this->sortBy))
            ->with('medicalCenters')
            ->paginate($this->perPage);
        return $users;
    }

    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }

    public function filtersCount(): int
    {
        $filters = [
            $this->searchWord !== '',
            $this->medicalCenterId > 0,
            $this->showArchived === true
        ];
        return count(array_filter($filters));
    }

    public function clearFilters(): void
    {
        $this->medicalCenterId = 0;
        $this->searchWord = '';
        $this->showArchived = false;
    }
}
