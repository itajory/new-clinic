@php
    use App\Models\User;
@endphp
<div>
    @can('viewDashboard', User::class)
        <div class="flex flex-col gap-4">

            {{-- first row --}}
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-8">
                <div class="col-span-2 ">

                    <div class="w-full bg-white shadow-xl card h-[180px]">
                        <div class="card-body">
                            <h2 class="card-title">{{ __('Medical Centers') }}</h2>
                            <p>{{ __('We serve on') }}
                                <span
                                    class="mx-2 text-xl font-semibold text-info">{{ $totalStats['medical_centers'] }}</span>
                                {{ __('medical center') }}
                            </p>
                            <div class="justify-end card-actions">
                                <a href="{{ route('medical_center.index') }}"
                                    wire:navigate
                                    class="btn btn-primary">{{ __('More') }}</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-span-2 ">

                    <div class="w-full bg-white shadow-xl card h-[180px]">
                        <div class="card-body">
                            <h2 class="card-title">{{ __('Doctors') }}</h2>
                            <p>{{ __('We have') }}
                                <span
                                    class="mx-2 text-xl font-semibold text-info">{{ $totalStats['doctors'] }}</span>
                                {{ __('doctor') }}
                            </p>
                            <div class="justify-end card-actions">
                                <a href="{{ route('doctor.index') }}"
                                    wire:navigate
                                    class="btn btn-primary">{{ __('More') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 ">

                    <div class="w-full bg-white shadow-xl card h-[180px]">
                        <div class="card-body">
                            <h2 class="card-title">{{ __('Patients') }}</h2>
                            <p>{{ __('We serve') }}
                                <span
                                    class="mx-2 text-xl font-semibold text-info">{{ $totalStats['patients'] }}</span>
                                {{ __('patient') }}
                            </p>
                            <div class="justify-end card-actions">
                                <a href="{{ route('patient.index') }}"
                                    wire:navigate
                                    class="btn btn-primary">{{ __('More') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 ">

                    <div class="w-full bg-white shadow-xl card h-[180px]">
                        <div class="card-body">
                            <h2 class="card-title">{{ __('Patient Funds') }}</h2>
                            <p>{{ __('We have cooperation with') }}
                                <span
                                    class="mx-2 text-xl font-semibold text-info">{{ $totalStats['patient_funds'] }}</span>
                                {{ __('patient fund') }}
                            </p>
                            <div class="justify-end card-actions">
                                <a href="{{ route('patient_fund.index') }}"
                                    wire:navigate
                                    class="btn btn-primary">{{ __('More') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- second row --}}
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-8">
                <div
                    class="flex flex-col items-start justify-start col-span-3 gap-4">
                    <div class="w-full bg-white shadow-xl card">
                        <div class="card-body">
                            <x-mary-chart wire:model="appointmentsChart" />
                        </div>
                    </div>
                    <div class="w-full bg-white shadow-xl card">
                        <div class="card-body">
                            <x-mary-chart wire:model="doctorChart" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endcan
</div>
