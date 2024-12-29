@php
    use App\Models\Setting;
    use App\Models\Appointment;
    use App\Models\Bank;
    use App\Models\City;
    use App\Models\MedicalCenter;
    use App\Models\Patient;
    use App\Models\PatientFund;
    use App\Models\PrescriptionTemplate;
    use App\Models\Role;
    use App\Models\Treatment;
    use App\Models\User;

    $logo = Setting::getSetting('logo', 'https://picsum.photos/536/354');
    $name = Setting::getSetting('name', 'Clinic System');
@endphp
        <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-theme="cupcake"
      dir="{{ $languageDir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic&family=Caudex&family=Lato&family=David&display=swap">
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js">
    </script>

</head>

<body class="antialiased bg-cultured">
{{-- The navbar with `sticky` and `full-width` --}}
<x-mary-nav sticky
            full-width
            class="bg-white border-b-0">

    <x-slot:brand>
        {{-- Drawer toggle for "main-drawer" --}}
        <label for="main-drawer"
               class="mr-3 lg:hidden">
            <x-mary-icon name="o-bars-3"
                         class="cursor-pointer"/>
        </label>

        {{-- Brand --}}
        <div>
            <a href="{{ route('dashboard') }}"
               wire:navigate>
                {{-- <x-application-logo class="hidden h-8 lg:block" /> --}}
                <img src="{{ Storage::url($logo) }}"
                     class="hidden h-8 lg:block"
                     alt="{{ $name }}">
            </a>
        </div>
    </x-slot:brand>

    {{-- Right side actions --}}
    <x-slot:actions>
        <x-mary-dropdown icon="c-language"
                         class="btn-ghost btn-sm"
                         responsive>
            @forelse ($availablelanguages as $language)
                <x-mary-menu-item
                        href="{{ route('change.languages', $language->short) }}"
                        {{-- LaravelLocalization::getLocalizedURL($language->short, null, [], true) --}}
                        title="{{ $language->name }}"/>
            @empty
            @endforelse
        </x-mary-dropdown>
        <x-mary-button icon="o-bell"
                       class="btn-ghost btn-sm"
                       responsive/>
    </x-slot:actions>
</x-mary-nav>

{{-- The main content with `full-width` --}}
<x-mary-main with-nav
             full-width>

    {{-- This is a sidebar that works also as a drawer on small screens --}}
    {{-- Notice the `main-drawer` reference here --}}
    <x-slot:sidebar
            drawer="main-drawer"
            collapsible
            class="bg-white">
        <div class="block p-6 lg:hidden">
            <a href="{{ route('dashboard') }}"
               wire:navigate>
                {{-- <x-application-logo class="h-8 " /> --}}
                <img src="{{ Storage::url($logo) }}"
                     class="h-8"
                     alt="{{ $name }}">
            </a>
        </div>

        {{-- User --}}
        @if ($user = auth()->user())
            <x-mary-list-item :item="$user"
                              value="name"
                              no-separator
                              no-hover
                              class="pt-2">
                <x-slot:actions>
                    <x-mary-button icon="o-power"
                                   class="btn-circle btn-ghost btn-xs"
                                   tooltip-left="logoff"
                                   link="{{ route('logout') }}"/>
                </x-slot:actions>
            </x-mary-list-item>

            <x-mary-menu-separator/>
        @endif

        {{-- Activates the menu item when a route matches the `link` property --}}
        <x-mary-menu activate-by-route
                     active-bg-color="bg-accent font-bold">

            {{-- @can('viewDashboard', User::class) --}}
            <x-mary-menu-item title="{{ __('Dashboard') }}"
                              icon="m-squares-2x2"
                              class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                              link="{{ route('dashboard') }}"
                              route="dashboard"/>
            {{-- @endcan --}}

            @canany(['viewAny', 'viewAny'], [User::class, Role::class])
                <x-mary-menu-sub title="{{ __('users_management') }}"
                                 icon="s-users">
                    @can('viewAny', User::class)
                        <x-mary-menu-item title="{{ __('users') }}"
                                          icon="s-users"
                                          class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                          link="{{ route('user-management.index') }}"
                                          route="user-management.index"/>
                    @endcan
                    @can('viewAny', Role::class)
                        <x-mary-menu-item title="{{ __('roles') }}"
                                          icon="s-users"
                                          class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                          link="{{ route('user-management.roles') }}"
                                          route="user-management.roles"/>
                    @endcan
                </x-mary-menu-sub>
            @endcanany

            @can('viewAny', City::class)
                <x-mary-menu-item title="{{ __('cities') }}"
                                  icon="s-map-pin"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('city.index') }}"
                                  route="city.index"/>
            @endcan
            @can('viewAny', Treatment::class)
                <x-mary-menu-item title="{{ __('treatments') }}"
                                  icon="c-briefcase"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('treatment.index') }}"
                                  route="treatment.index"/>
            @endcan
            @can('viewAny', Bank::class)
                <x-mary-menu-item title="{{ __('banks') }}"
                                  icon="c-building-library"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('bank.index') }}"
                                  route="bank.index"/>
            @endcan
            @can('viewAny', PrescriptionTemplate::class)
                <x-mary-menu-item title="{{ __('prescription_templates') }}"
                                  icon="c-clipboard-document-list"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('prescription.index') }}"
                                  route="prescription.index"/>
            @endcan
            @can('viewAny', PatientFund::class)
                <x-mary-menu-item title="{{ __('patient_funds') }}"
                                  icon="c-gift-top"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('patient_fund.index') }}"
                                  route="patient_fund.index"/>
            @endcan
            @can('viewAny', MedicalCenter::class)
                <x-mary-menu-item title="{{ __('medical_centers') }}"
                                  icon="m-building-office-2"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('medical_center.index') }}"
                                  route="medical_center.index"/>
            @endcan
            @can('viewAny', User::class)
                <x-mary-menu-item title="{{ __('doctors') }}"
                                  icon="s-user"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('doctor.index') }}"
                                  route="doctor.index"/>
            @endcan

            @if (auth()->user()->role_id == 2)
                <x-mary-menu-item title="{{ __('My Profile') }}"
                                  icon="s-user"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('doctors.view', auth()->user()->id) }}"
                                  route="doctors.view"/>
            @endif
            @can('viewAny', Patient::class)
                <x-mary-menu-item title="{{ __('patients') }}"
                                  icon="s-user"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('patient.index') }}"
                                  route="patient.index"/>
            @endcan
            @can('viewAny', Appointment::class)
                <x-mary-menu-item title="{{ __('appointments') }}"
                                  icon="c-calendar-days"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('appointment.index') }}"
                                  route="appointment.index"/>
            @endcan
            @can('viewAny', User::class)
                <x-mary-menu-item title="{{ __('systemlogs') }}"
                                  icon="o-clipboard"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('systemlogs') }}"
                                  route="systemlogs"/>
            @endcan
            @can('viewAny', Setting::class)
                <x-mary-menu-item title="{{ __('Settings') }}"
                                  icon="c-wrench-screwdriver"
                                  class="hover:bg-accent hover:font-bold active:!bg-primary active:!font-bold active:!text-white transition-all duration-300"
                                  link="{{ route('setting.index') }}"
                                  route="setting.index"/>
            @endcan
        </x-mary-menu>
    </x-slot:sidebar>

    {{-- The `$slot` goes here --}}
    <x-slot:content>
        {{ $slot }}
    </x-slot:content>
</x-mary-main>

{{--  TOAST area --}}
<x-mary-toast/>
@livewireScripts
{{-- <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script> --}}

{{-- Remove or comment out the following line --}}
{{-- @vite(['resources/js/app.js']) --}}

@stack('scripts')
</body>

</html>
