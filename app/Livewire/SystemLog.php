<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SystemLog as ModelsSystemLog;

class SystemLog extends Component
{
    public array $headers;
    public string $searchWord = '';

    public int $perPage;
    public array $perPageOptions;
    public array $sortBy;



    public function mount()
    {
        // $this->authorize('viewAny', ModelsSystemLog::class);
        $this->headers = [
            ['key' => 'created_at', 'label' => __('Created At')],
            ['key' => 'user.name', 'label' => __('User')],
            ['key' => 'event_description', 'label' => __('Event Description')],
            ['key' => 'table_id', 'label' => __('Record ID')],
        ];
        $this->perPageOptions = [30, 20, 50, 100];
        $this->perPage = $this->perPageOptions[0];
        $this->sortBy = ['column' => 'created_at', 'direction' => 'desc', 'class' => 'text-red-500'];

        $this->systemLogs();
    }





    public function render()
    {
        return view('livewire.system-log')->layout('layouts.dash');
    }

    public function systemLogs()
    {
        $logs = ModelsSystemLog::with(['user', 'appointment', 'medicalCenter'])
            ->where(function ($query) {
                $query->whereRaw('LOWER(event_description) LIKE ?', ['%' . strtolower($this->searchWord) . '%'])
                    ->orWhereHas('user', function ($q) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->searchWord) . '%']);
                    });
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
        return $logs;
    }



    public function getRowDecoration(): array
    {
        return [
            'hover:!bg-accent' => fn($role) => true
        ];
    }
}
