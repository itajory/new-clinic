<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use App\Models\Patient;
use Livewire\Component;
use App\Models\Appointment;
use App\Models\PatientFund;
use App\Models\MedicalCenter;

class DashboardIndex extends Component
{


    public array $appointmentsChart = [];
    public array $doctorChart = [];
    public array $totalStats = [];

    public function mount()
    {
        if (auth()->user()->can('viewDashboard', User::class)) {
            $this->loadAppointmentChartByMonth();
            $this->loadAppointmentChartByDoctor();
            $this->totalStats = $this->getTotalStats();
        }
    }
    public function render()
    {
        return view('livewire.dashboard.dashboard-index')->layout('layouts.dash');
    }

    public function getAppointmentsCountBerMonth()
    {
        $counts = Appointment::selectRaw('MONTHNAME(appointment_time) as month, COUNT(*) as total')
            ->groupBy('month')
            ->get()
            ->toArray();
        return $counts;
    }

    public function loadAppointmentChartByMonth()
    {
        $counts = $this->getAppointmentsCountBerMonth();

        $label = array_column($counts, 'month');
        $data = array_column($counts, 'total');
        $this->appointmentsChart = [
            'type' => 'line',
            'data' => [
                'labels' => $label,
                'datasets' => [
                    [
                        'label' => trans('Appointments'),
                        'data' => $data,
                    ]
                ]
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => trans('Appointments by Month')
                    ]
                ]
            ]
        ];
    }
    public function getAppointmentsCountByDoctor()
    {
        $counts = Appointment::selectRaw('users.name as doctor, COUNT(*) as total')
            ->join('users', 'appointments.doctor_id', '=', 'users.id')
            ->groupBy('users.name')
            ->get()
            ->toArray();
        return $counts;
    }

    public function loadAppointmentChartByDoctor()
    {
        $counts = $this->getAppointmentsCountByDoctor();

        $label = array_column($counts, column_key: 'doctor');
        $data = array_column($counts, 'total');
        $this->doctorChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $label,
                'datasets' => [
                    [
                        'label' => trans('Appointments'),
                        'data' => $data,
                    ]
                ]
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => trans('Appointments by Doctor')
                    ]
                ]
            ]
        ];
    }


    public function getTotalStats()
    {
        return [
            'medical_centers' => MedicalCenter::count(),
            'doctors' => User::whereHas('role', function ($query) {
                $query->where('name', 'doctor');
            })->count(),
            'patients' => Patient::count(),
            'patient_funds' => PatientFund::count()
        ];
    }
}
