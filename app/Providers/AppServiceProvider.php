<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Bank;
use App\Models\City;
use App\Models\Language;
use App\Models\MedicalCenter;
use App\Models\Patient;
use App\Models\PatientFund;
use App\Models\PrescriptionTemplate;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Treatment;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\BankObserver;
use App\Observers\CityObserver;
use App\Observers\MedicalCenterObserver;
use App\Observers\PatientFundObserver;
use App\Observers\PatientObserver;
use App\Observers\PrescriptionTemplateObserver;
use App\Observers\RoleObserver;
use App\Observers\TreatmentObserver;
use App\Observers\UserObserver;
use App\Rules\EnumRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add this in your AppServiceProvider to identify slow queries
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {  // Log queries taking more than 100ms
                    Log::warning('Slow query: ' . $query->sql);
                }
            });
        }

        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Patient::observe(PatientObserver::class);
        City::observe(CityObserver::class);
        Treatment::observe(TreatmentObserver::class);
        Appointment::observe(AppointmentObserver::class);
        PrescriptionTemplate::observe(PrescriptionTemplateObserver::class);
        Bank::observe(BankObserver::class);
        MedicalCenter::observe(MedicalCenterObserver::class);
        PatientFund::observe(PatientFundObserver::class);
        View::composer('*', function ($view) {
            $setting = Cache::remember('site_settings', 60 * 24, function () {
                return optional(Setting::find(1));
            });

            $def_lang = Cache::remember('default_language', 60 * 24, function () use ($setting) {
                return optional($setting)->default_language;
            });

            $currentlanguage = app()->getLocale() ?? 'en';

            $availablelanguages = Cache::remember('available_languages', 60 * 24, function () {
                return Language::where('status', 1)->get();
            });

            $language = Cache::remember('current_language_' . $currentlanguage, 60 * 24,
                function () use ($currentlanguage) {
                    return Language::where('status', 1)
                        ->where('short', $currentlanguage)
                        ->first();
                });

            $languageName = $language ? $language->name : null;
            $languageShort = $language ? $language->short : null;
            $languageDir = $language ? $language->dir : null;
//            $setting = optional(Setting::find(1));
//            $def_lang = optional($setting)->default_language;
//            $currentlanguage = app()->getLocale() ?? 'en';
//            $availablelanguages = Language::where('status', 1)->get();
//            $language = Language::where('status', 1)->where('short', $currentlanguage)->first();
//            $languageName = $language ? $language->name : null;
//            $languageShort = $language ? $language->short : null;
//            $languageDir = $language ? $language->dir : null;

            $view->with([
                'site_name' => $setting->name,
                'site_description' => $setting->description,
                'site_keywords' => $setting->keywords,
                'site_logo' => asset($setting->logo ? $setting->logo : 'assets/site/default_logo.png'),
                'site_icon' => asset($setting->icon ? $setting->icon : 'assets/site/default_icon.png'),
                'availablelanguages' => $availablelanguages,
                'languageName' => $languageName,
                'languageShort' => $languageShort,
                'languageDir' => $languageDir,
                'test_lang' => $currentlanguage,
            ]);
        });
        Validator::extend('enum', function ($attribute, $value, $parameters, $validator) {
            $enumClass = $parameters[0];

            return (new EnumRule($enumClass))->passes($attribute, $value);
        });
    }
}
