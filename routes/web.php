<?php

use App\Livewire\Appointment\AppointmentIndex;
use App\Livewire\Appointment\NewAppointmentIndex;
use App\Livewire\Bank\BankIndex;
use App\Livewire\City\CityIndex;
use App\Livewire\Dashboard;
use App\Livewire\Dashboard\DashboardIndex;
use App\Livewire\Doctor\DoctorIndex;
use App\Livewire\Doctor\DoctorView;
use App\Livewire\Doctor\DoctorViewNew;
use App\Livewire\Doctor\Schedule;
use App\Livewire\MedicalCenter\MedicalCenterIndex;
use App\Livewire\Patient\CreatePatient;
use App\Livewire\Patient\PatientIndex;
use App\Livewire\Patient\ViewPatient;
use App\Livewire\PatientFund\PatientFundIndex;
use App\Livewire\PatientFund\ViewPatientFund;
use App\Livewire\Prescription\PrescriptionIndex;
use App\Livewire\Setting\SettingIndex;
use App\Livewire\SystemLog;
use App\Livewire\Treatment\TreatmentIndex;
use App\Livewire\UserManagement\RolesIndex;
use App\Livewire\UserManagement\UsersIndex;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/change-languages/{short}', function ($short) {
    $languages = Language::where('status', 1)->pluck('short')->toArray();
    if (in_array($short, $languages)) {
        Session::put('lang', $short);
        App::setLocale(strtolower($short));
    }

    return redirect()->back();
})->name('change.languages');

Route::view('/', 'welcome');
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

Route::get('logout', function () {
    Auth::guard('web')->logout();
    Session::invalidate();
    Session::regenerateToken();

    return redirect('/');
})->middleware(['auth'])->name('logout');

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // Route::view('', 'dashboard')->name('dashboard');
    Route::get('', DashboardIndex::class)->name('dashboard');
    Route::get('settings', SettingIndex::class)->name('setting.index');
    Route::get('user-management', UsersIndex::class)->name('user-management.index');
    Route::get('user-management/roles', RolesIndex::class)->name('user-management.roles');
    Route::get('cities', CityIndex::class)->name('city.index');
    Route::get('treatments', TreatmentIndex::class)->name('treatment.index');
    Route::get('banks', BankIndex::class)->name('bank.index');
    Route::get('prescriptions', PrescriptionIndex::class)->name('prescription.index');
    Route::get('patient_funds', PatientFundIndex::class)->name('patient_fund.index');
    Route::get('patient_funds/{id}', ViewPatientFund::class)->name('patient_fund.view');
    Route::get('medical-centers', MedicalCenterIndex::class)->name('medical_center.index');
    Route::get('doctors', DoctorIndex::class)->name('doctor.index');
    //    Route::get('doctors/{id}', DoctorView::class)->name('doctors.view');
    Route::get('doctors/{id}', DoctorViewNew::class)->name('doctors.view');
    // Route::get('doctor/{id}', Schedule::class)->name('doctor.view');
    Route::get('patients', PatientIndex::class)->name('patient.index');
    Route::get('patients/create', CreatePatient::class)->name('patient.create');
    Route::get('patients/{id}', ViewPatient::class)->name('patient.view');
    Route::get('patients/edit/{id}', CreatePatient::class)->name('patient.update');
    // Route::get('appointments', NewAppointmentIndex::class)->name('appointment.index');
    Route::get('appointments', AppointmentIndex::class)->name('appointment.index');
    //    Route::get('appointments/new', NewAppointmentIndex::class)->name('appointment.index.new');
    Route::get('system-logs', SystemLog::class)->name('systemlogs');
});

require __DIR__.'/auth.php';
