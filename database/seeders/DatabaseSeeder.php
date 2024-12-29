<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Role;
use App\Models\User;
use App\Models\Patient;
use App\Models\Setting;
use App\Models\Treatment;
use App\Models\Permission;
use App\Models\Appointment;
use App\Models\PatientFund;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WorkingHour;
use App\Models\MedicalCenter;
use App\Models\DoctorSchedule;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(LanguageSeeder::class);


        $adminRole = Role::create(['name' => 'admin']);
        $doctor = Role::create(['name' => 'doctor']);
        $receptionistRole = Role::create(['name' => 'receptionist']);
        $accountant = Role::create(['name' => 'accountant']);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);
        ////////////////////////////////////////////////////////////////  this data is for testing purposes
        $medicalCenter = MedicalCenter::factory()->create();
        $medicalCenter2 = MedicalCenter::factory()->create();
        $medicalCenter3 = MedicalCenter::factory()->create();


        $receptionistUser = User::factory()->create([
            'name' => 'Receptionist User',
            'email' => 'rc@gmail.com',
            'password' => bcrypt('12345678'),
            'role_id' => $receptionistRole->id,
        ]);

        $treatmet1 = Treatment::create([
            'name' => 'Treatment 1',
            'price' => 100,
            'duration' => 30,
        ]);
        $treatmet2 = Treatment::create([
            'name' => 'Treatment 2',
            'price' => 200,
            'duration' => 60,
        ]);
        $treatmet3 = Treatment::create([
            'name' => 'Treatment 3',
            'price' => 300,
            'duration' => 90,
        ]);

        $doctorUser1 = User::factory()->create([
            'name' => 'Doctor User',
            'email' => 'dr@gmail.com',
            'password' => bcrypt('12345678'),
            'role_id' => $doctor->id,
            'treatment_id' => $treatmet1->id,
        ]);
        $doctorUser1->medicalCenters()->attach($medicalCenter->id);
        $doctorUser1->medicalCenters()->attach($medicalCenter2->id);
        $doctorUser1->medicalCenters()->attach($medicalCenter3->id);
        $city1 = City::factory()->create([
            'name' => 'City 1',
        ]);
        $city2 = City::factory()->create([
            'name' => 'City 2',
        ]);
        $city3 = City::factory()->create([
            'name' => 'City 3',
        ]);
        $patient1 = Patient::create([
            'full_name' => 'Patient full name 1',
            'gender' => 'male',
            'id_number' => '123456789',
            'patient_phone' => '123456789',
            'guardian_phone' => '123456789',
            'birth_date' => '1990-01-01',
            'city_id' => $city1->id,
        ]);
        $patient2 = Patient::create([
            'full_name' => 'Patient full name 2',
            'gender' => 'female',
            'id_number' => '123776789',
            'patient_phone' => '123245689',
            'guardian_phone' => '123454789',
            'birth_date' => '1998-03-01',
            'city_id' => $city2->id,
        ]);
        $patientFund1 = PatientFund::create([
            'name' => 'Patient Fund 1',
            'contribution_type' => 'percentage',
        ]);
        $patientFund2 = PatientFund::create([
            'name' => 'Patient Fund 2',
            'contribution_type' => 'fixed',
        ]);
        $patientFund3 = PatientFund::create([
            'name' => 'Patient Fund 3',
            'contribution_type' => 'percentage',
        ]);

        foreach (range(1, 7) as $index) {
            WorkingHour::create([
                'day_of_week' => $index,
                'opening_time' => '08:00',
                'closing_time' => '16:00',
                'medical_center_id' => $medicalCenter->id,
            ]);
        }
        foreach (range(1, 7) as $index) {
            WorkingHour::create([
                'day_of_week' => $index,
                'opening_time' => '08:00',
                'closing_time' => '20:00',
                'medical_center_id' => $medicalCenter2->id,
            ]);

            DoctorSchedule::create([
                'day_of_week' => $index,
                'start_time' => '09:00',
                'end_time' => '18:00',
                'medical_center_id' => $medicalCenter2->id,
                'user_id' => $doctorUser1->id,
            ]);
        }

        foreach (range(1, 7) as $index) {
            WorkingHour::create([
                'day_of_week' => $index,
                'opening_time' => '09:00',
                'closing_time' => '17:00',
                'medical_center_id' => $medicalCenter3->id,
            ]);
            DoctorSchedule::create([
                'day_of_week' => $index,
                'start_time' => '10:00',
                'end_time' => '15:00',
                'medical_center_id' => $medicalCenter3->id,
                'user_id' => $doctorUser1->id,
            ]);
        }

        foreach (range(1, 7) as $index) {
            Appointment::create([
                'patient_id' => $patient1->id,
                'doctor_id' => $doctorUser1->id,
                'medical_center_id' => $medicalCenter3->id,
                'treatment_id' => 1,
                'appointment_time' => now()->addHours($index)->addMinutes($index * 15),
                'duration' => 30,
                'status' => 'reserved',
                'created_by' => $receptionistUser->id,
                'price' => 100,
                'discount' => 0,
                'patient_fund_id' => $patientFund1->id,
                'patient_fund_amount' => 10,
                'patient_fund_total' => 10,
                'total' => 90,
                'patient_fund_contribution_type' => 'percentage',
            ]);
        }

        //////////////////////////////////////////////////////////////// end of testing data

        // default settings name, logo, emails, phones address,langugs
        $st1 = Setting::create(['key' => 'name', 'value' => 'Ajory Clinic System']);
        $st3 = Setting::create(['key' => 'email', 'value' => 'info@ajory.net']);
        $st4 = Setting::create(['key' => 'phone', 'value' => '0123456789']);
        $st5 = Setting::create(['key' => 'address', 'value' => '123,Al-Tahreer Gaza, Palestine ']);
        $st6 = Setting::create(['key' => 'languages', 'value' => 'ar,en']);
        $st2 = Setting::create(['key' => 'logo', 'value' => 'Ajory Clinic System']);
        // $st2 = Setting::create(['key' => 'favicon', 'value' => 'Ajory Clinic System']);


        // permissions
        // users table permissions
        $createUserPermission = Permission::create(['name' => 'create', 'table_name' => 'users']);
        $updateUserPermission = Permission::create(['name' => 'update', 'table_name' => 'users']);
        $deleteUserPermission = Permission::create(['name' => 'delete', 'table_name' => 'users']);
        $viewUserPermission = Permission::create(['name' => 'view', 'table_name' => 'users']);
        $restoreUserPermission = Permission::create(['name' => 'restore', 'table_name' => 'users']);
        $forceDeleteUserPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'users']);
        $viewAnyUserPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'users']);
        // roles table permissionspatient_funds
        $createRolePermission = Permission::create(['name' => 'create', 'table_name' => 'roles']);
        $updateRolePermission = Permission::create(['name' => 'update', 'table_name' => 'roles']);
        $deleteRolePermission = Permission::create(['name' => 'delete', 'table_name' => 'roles']);
        $viewRolePermission = Permission::create(['name' => 'view', 'table_name' => 'roles']);
        $restoreRolePermission = Permission::create(['name' => 'restore', 'table_name' => 'roles']);
        $forceDeleteRolePermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'roles']);
        $viewAnyRolePermission = Permission::create(['name' => 'viewAny', 'table_name' => 'roles']);
        // permissions table permissions
        $createPermissionPermission = Permission::create(['name' => 'create', 'table_name' => 'permissions']);
        $updatePermissionPermission = Permission::create(['name' => 'update', 'table_name' => 'permissions']);
        $deletePermissionPermission = Permission::create(['name' => 'delete', 'table_name' => 'permissions']);
        $viewPermissionPermission = Permission::create(['name' => 'view', 'table_name' => 'permissions']);
        $restorePermissionPermission = Permission::create(['name' => 'restore', 'table_name' => 'permissions']);
        $forceDeletePermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'permissions']);
        $viewAnyPermissionPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'permissions']);
        // medical_centers table permissions
        $createMedicalCenterPermission = Permission::create(['name' => 'create', 'table_name' => 'medical_centers']);
        $updateMedicalCenterPermission = Permission::create(['name' => 'update', 'table_name' => 'medical_centers']);
        $deleteMedicalCenterPermission = Permission::create(['name' => 'delete', 'table_name' => 'medical_centers']);
        $forceMedicalCenterPermission = Permission::create([
            'name' => 'forceDelete',
            'table_name' => 'medical_centers',
        ]);
        $restoreMedicalCenterPermission = Permission::create(['name' => 'restore', 'table_name' => 'medical_centers']);
        $viewMedicalCenterPermission = Permission::create(['name' => 'view', 'table_name' => 'medical_centers']);
        $viewAnyMedicalCenterPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'medical_centers']);

        // cities table permissions
        $createCityPermission = Permission::create(['name' => 'create', 'table_name' => 'cities']);
        $updateCityPermission = Permission::create(['name' => 'update', 'table_name' => 'cities']);
        $deleteCityPermission = Permission::create(['name' => 'delete', 'table_name' => 'cities']);
        $viewCityPermission = Permission::create(['name' => 'view', 'table_name' => 'cities']);
        $restoreCityPermission = Permission::create(['name' => 'restore', 'table_name' => 'cities']);
        $forceDeleteCityPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'cities']);
        $viewAnyCityPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'cities']);

        // treatment table permissions
        $createTreatmentPermission = Permission::create(['name' => 'create', 'table_name' => 'treatments']);
        $updateTreatmentPermission = Permission::create(['name' => 'update', 'table_name' => 'treatments']);
        $deleteTreatmentPermission = Permission::create(['name' => 'delete', 'table_name' => 'treatments']);
        $viewTreatmentPermission = Permission::create(['name' => 'view', 'table_name' => 'treatments']);
        $restoreTreatmentPermission = Permission::create(['name' => 'restore', 'table_name' => 'treatments']);
        $forceDeleteTreatmentPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'treatments']);
        $viewAnyTreatmentPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'treatments']);
        // bank table permissions
        $createBankPermission = Permission::create(['name' => 'create', 'table_name' => 'banks']);
        $updateBankPermission = Permission::create(['name' => 'update', 'table_name' => 'banks']);
        $deleteBankPermission = Permission::create(['name' => 'delete', 'table_name' => 'banks']);
        $viewBankPermission = Permission::create(['name' => 'view', 'table_name' => 'banks']);
        $restoreBankPermission = Permission::create(['name' => 'restore', 'table_name' => 'banks']);
        $forceDeleteBankPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'banks']);
        $viewAnyBankPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'banks']);
        // prescription templates table permissions
        $createPrescriptionTemplatePermission = Permission::create([
            'name' => 'create',
            'table_name' => 'prescription_templates',
        ]);
        $updatePrescriptionTemplatePermission = Permission::create([
            'name' => 'update',
            'table_name' => 'prescription_templates',
        ]);
        $deletePrescriptionTemplatePermission = Permission::create([
            'name' => 'delete',
            'table_name' => 'prescription_templates',
        ]);
        $viewPrescriptionTemplatePermission = Permission::create([
            'name' => 'view',
            'table_name' => 'prescription_templates',
        ]);
        $restorePrescriptionTemplatePermission = Permission::create([
            'name' => 'restore',
            'table_name' => 'prescription_templates',
        ]);
        $forceDeletePrescriptionTemplatePermission = Permission::create([
            'name' => 'forceDelete',
            'table_name' => 'prescription_templates',
        ]);
        $viewAnyPrescriptionTemplatePermission = Permission::create([
            'name' => 'viewAny',
            'table_name' => 'prescription_templates',
        ]);
        // patient fund table permissions
        $createPatientFundPermission = Permission::create(['name' => 'create', 'table_name' => 'patient_funds']);
        $updatePatientFundPermission = Permission::create(['name' => 'update', 'table_name' => 'patient_funds']);
        $deletePatientFundPermission = Permission::create(['name' => 'delete', 'table_name' => 'patient_funds']);
        $viewPatientFundPermission = Permission::create(['name' => 'view', 'table_name' => 'patient_funds']);
        $restorePatientFundPermission = Permission::create(['name' => 'restore', 'table_name' => 'patient_funds']);
        $forceDeletePatientFundPermission = Permission::create([
            'name' => 'forceDelete',
            'table_name' => 'patient_funds',
        ]);
        $viewAnyPatientFundPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'patient_funds']);
        // patient table permissions
        $createPatientPermission = Permission::create(['name' => 'create', 'table_name' => 'patients']);
        $updatePatientPermission = Permission::create(['name' => 'update', 'table_name' => 'patients']);
        $deletePatientPermission = Permission::create(['name' => 'delete', 'table_name' => 'patients']);
        $viewPatientPermission = Permission::create(['name' => 'view', 'table_name' => 'patients']);
        $restorePatientPermission = Permission::create(['name' => 'restore', 'table_name' => 'patients']);
        $forceDeletePatientPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'patients']);
        $viewAnyPatientPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'patients']);

        // working_hours table permissions
        $createWorkingHourPermission = Permission::create(['name' => 'create', 'table_name' => 'working_hours']);
        $updateWorkingHourPermission = Permission::create(['name' => 'update', 'table_name' => 'working_hours']);
        $deleteWorkingHourPermission = Permission::create(['name' => 'delete', 'table_name' => 'working_hours']);
        $viewWorkingHourPermission = Permission::create(['name' => 'view', 'table_name' => 'working_hours']);
        $restoreWorkingHourPermission = Permission::create(['name' => 'restore', 'table_name' => 'working_hours']);
        $forceDeleteWorkingHourPermission = Permission::create([
            'name' => 'forceDelete',
            'table_name' => 'working_hours'
        ]);
        $viewAnyWorkingHourPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'working_hours']);

        // doctor schedule table permissions
        $createDoctorSchedulePermission = Permission::create(['name' => 'create', 'table_name' => 'doctor_schedules']);
        $updateDoctorSchedulePermission = Permission::create(['name' => 'update', 'table_name' => 'doctor_schedules']);
        $deleteDoctorSchedulePermission = Permission::create(['name' => 'delete', 'table_name' => 'doctor_schedules']);
        $viewDoctorSchedulePermission = Permission::create(['name' => 'view', 'table_name' => 'doctor_schedules']);
        $restoreDoctorSchedulePermission = Permission::create([
            'name' => 'restore',
            'table_name' => 'doctor_schedules'
        ]);
        $forceDeleteDoctorSchedulePermission = Permission::create([
            'name' => 'forceDelete',
            'table_name' => 'doctor_schedules'
        ]);
        $viewAnyDoctorSchedulePermission = Permission::create(['name' => 'viewAny', 'table_name' => 'doctor_schedules']);

        // appointments table permissions
        $createAppointmentPermission = Permission::create(['name' => 'create', 'table_name' => 'appointments']);
        $updateAppointmentPermission = Permission::create(['name' => 'update', 'table_name' => 'appointments']);
        $deleteAppointmentPermission = Permission::create(['name' => 'delete', 'table_name' => 'appointments']);
        $viewAppointmentPermission = Permission::create(['name' => 'view', 'table_name' => 'appointments']);
        $restoreAppointmentPermission = Permission::create(['name' => 'restore', 'table_name' => 'appointments']);
        $forceDeleteAppointmentPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'appointments']);
        $viewAnyAppointmentPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'appointments']);

        // payments table permissions
        $createPaymentPermission = Permission::create(['name' => 'create', 'table_name' => 'payments']);
        $updatePaymentPermission = Permission::create(['name' => 'update', 'table_name' => 'payments']);
        $deletePaymentPermission = Permission::create(['name' => 'delete', 'table_name' => 'payments']);
        $viewPaymentPermission = Permission::create(['name' => 'view', 'table_name' => 'payments']);
        $restorePaymentPermission = Permission::create(['name' => 'restore', 'table_name' => 'payments']);
        $forceDeletePaymentPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'payments']);
        $viewAnyPaymentPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'payments']);

        // checks table permissions
        $createCheckPermission = Permission::create(['name' => 'create', 'table_name' => 'checks']);
        $updateCheckPermission = Permission::create(['name' => 'update', 'table_name' => 'checks']);
        $deleteCheckPermission = Permission::create(['name' => 'delete', 'table_name' => 'checks']);
        $viewCheckPermission = Permission::create(['name' => 'view', 'table_name' => 'checks']);
        $restoreCheckPermission = Permission::create(['name' => 'restore', 'table_name' => 'checks']);
        $forceDeleteCheckPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'checks']);
        $viewAnyCheckPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'checks']);

        // patient recors table permissions
        $createPatientRecordPermission = Permission::create(['name' => 'create', 'table_name' => 'patient_records']);
        $updatePatientRecordPermission = Permission::create(['name' => 'update', 'table_name' => 'patient_records']);
        $deletePatientRecordPermission = Permission::create(['name' => 'delete', 'table_name' => 'patient_records']);
        $viewPatientRecordPermission = Permission::create(['name' => 'view', 'table_name' => 'patient_records']);
        $restorePateintRecordPermission = Permission::create(['name' => 'restore', 'table_name' => 'patient_records']);
        $forceDeletePatientRecordPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'patient_records']);
        $viewAnyPatientRecordPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'patient_records']);
        //settings table permissions
        $viewSettingPermission = Permission::create(['name' => 'view', 'table_name' => 'settings']);
        $createSettingPermission = Permission::create(['name' => 'create', 'table_name' => 'settings']);
        $updateSettingPermission = Permission::create(['name' => 'update', 'table_name' => 'settings']);
        $deleteSettingPermission = Permission::create(['name' => 'delete', 'table_name' => 'settings']);
        $restoreSettingPermission = Permission::create(['name' => 'restore', 'table_name' => 'settings']);
        $viewAnySettingPermission = Permission::create(['name' => 'viewAny', 'table_name' => 'settings']);
        $forceDeleteSettingPermission = Permission::create(['name' => 'forceDelete', 'table_name' => 'settings']);

        //system logs table permissions
        $viewSystemLogPermission = Permission::create(['name' => 'view', 'table_name' => 'system_logs']);



        $adminRole->permissions()->attach([
            $createUserPermission->id,
            $updateUserPermission->id,
            $deleteUserPermission->id,
            $viewUserPermission->id,
            $createRolePermission->id,
            $updateRolePermission->id,
            $deleteRolePermission->id,
            $viewRolePermission->id,
            $createPermissionPermission->id,
            $updatePermissionPermission->id,
            $deletePermissionPermission->id,
            $viewPermissionPermission->id,
            $createMedicalCenterPermission->id,
            $updateMedicalCenterPermission->id,
            $deleteMedicalCenterPermission->id,
            $viewMedicalCenterPermission->id,
            $createCityPermission->id,
            $updateCityPermission->id,
            $deleteCityPermission->id,
            $viewCityPermission->id,
            $restoreCityPermission->id,
            $forceDeleteCityPermission->id,
            $restoreMedicalCenterPermission->id,
            $forceMedicalCenterPermission->id,
            $restoreUserPermission->id,
            $forceDeleteUserPermission->id,
            $restoreRolePermission->id,
            $forceDeleteRolePermission->id,
            $restorePermissionPermission->id,
            $forceDeletePermission->id,
            $createTreatmentPermission->id,
            $updateTreatmentPermission->id,
            $deleteTreatmentPermission->id,
            $viewTreatmentPermission->id,
            $restoreTreatmentPermission->id,
            $forceDeleteTreatmentPermission->id,
            $createBankPermission->id,
            $updateBankPermission->id,
            $deleteBankPermission->id,
            $viewBankPermission->id,
            $restoreBankPermission->id,
            $forceDeleteBankPermission->id,
            $createPrescriptionTemplatePermission->id,
            $updatePrescriptionTemplatePermission->id,
            $deletePrescriptionTemplatePermission->id,
            $viewPrescriptionTemplatePermission->id,
            $restorePrescriptionTemplatePermission->id,
            $forceDeletePrescriptionTemplatePermission->id,
            $createPatientFundPermission->id,
            $updatePatientFundPermission->id,
            $deletePatientFundPermission->id,
            $viewPatientFundPermission->id,
            $restorePatientFundPermission->id,
            $forceDeletePatientFundPermission->id,
            $createPatientPermission->id,
            $updatePatientPermission->id,
            $deletePatientPermission->id,
            $viewPatientPermission->id,
            $restorePatientPermission->id,
            $forceDeletePatientPermission->id,
            $createWorkingHourPermission->id,
            $updateWorkingHourPermission->id,
            $deleteWorkingHourPermission->id,
            $viewWorkingHourPermission->id,
            $restoreWorkingHourPermission->id,
            $forceDeleteWorkingHourPermission->id,
            $createDoctorSchedulePermission->id,
            $updateDoctorSchedulePermission->id,
            $deleteDoctorSchedulePermission->id,
            $viewDoctorSchedulePermission->id,
            $restoreDoctorSchedulePermission->id,
            $forceDeleteDoctorSchedulePermission->id,
            $createAppointmentPermission->id,
            $updateAppointmentPermission->id,
            $deleteAppointmentPermission->id,
            $viewAppointmentPermission->id,
            $restoreAppointmentPermission->id,
            $forceDeleteAppointmentPermission->id,
            $createPaymentPermission->id,
            $updatePaymentPermission->id,
            $deletePaymentPermission->id,
            $viewPaymentPermission->id,
            $restorePaymentPermission->id,
            $forceDeletePaymentPermission->id,
            $createCheckPermission->id,
            $updateCheckPermission->id,
            $deleteCheckPermission->id,
            $viewCheckPermission->id,
            $restoreCheckPermission->id,
            $forceDeleteCheckPermission->id,
            $createPatientRecordPermission->id,
            $updatePatientRecordPermission->id,
            $deletePatientRecordPermission->id,
            $viewPatientRecordPermission->id,
            $restorePateintRecordPermission->id,
            $forceDeletePatientRecordPermission->id,
            $viewSystemLogPermission->id,
            $viewAnyAppointmentPermission->id,
            $viewAnyBankPermission->id,
            $viewAnyCheckPermission->id,
            $viewAnyCityPermission->id,
            $viewAnyDoctorSchedulePermission->id,
            $viewAnyMedicalCenterPermission->id,
            $viewAnyPatientPermission->id,
            $viewAnyPaymentPermission->id,
            $viewAnyPatientFundPermission->id,
            $viewAnyPatientRecordPermission->id,
            $viewAnyPrescriptionTemplatePermission->id,
            $viewAnyTreatmentPermission->id,
            $viewAnyWorkingHourPermission->id,
            $viewAnyPermissionPermission->id,
            $viewAnyRolePermission->id,
            $viewAnyUserPermission->id,
            $viewAnySettingPermission->id,
            $viewSettingPermission->id,
            $deleteSettingPermission->id,
            $createSettingPermission->id,
            $updateSettingPermission->id,
            $forceDeleteSettingPermission->id,
            $restoreSettingPermission->id,
        ]);
    }
}
