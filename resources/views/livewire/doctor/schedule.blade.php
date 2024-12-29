<div>
    <div id="tbyb-abdulrahman-hassan" class="mb-10">
        <div class="flex flex-wrap gap-5 justify-between items-center">
            <div>
                <div class="text-4xl font-extrabold ">
                    {{ __('doctor') }} / {{ $name }}
                </div>

            </div>

            <div class="flex items-center gap-3 ">
                <div class="flex gap-1 flex-wrap">
                    <button type="button" @click="" class="btn normal-case btn-warning w-36">
                        {{ __('change_password') }}
                    </button>
                    <button type="button" @click="" class="btn normal-case btn-error w-36">
                        {{ __('delete') }}
                    </button><button type="button" @click="$wire.setEditMode(true)"
                        class="btn normal-case btn-info  w-36">
                        {{ __('edit') }}
                    </button>
                </div>
            </div>
        </div>
        <hr class="my-5">
        <div class="h-0.5 -mt-9 mb-9">
            <progress class="progress progress-primary w-full h-0.5 dark:h-1" wire:loading=""></progress>
        </div>
    </div>
    <form wire:submit="save" class="grid grid-flow-row auto-rows-min gap-3">

        <div class="grid grid-cols-1 lg:grid-cols-2 lg:space-x-4 space-y-8">
            <div>
                <div class="grid-cols-2 grid space-x-1 gap-3">
                    <div> <label class="pt-0 label label-text font-semibold">
                            <span>
                                {{ __('name') }}
                            </span>
                        </label>
                        <div class="flex-1 relative">
                            <input class="input input-primary w-full peer !bg-inherit" type="text" wire:model="name"
                                disabled="disabled">
                        </div>
                    </div>
                    <div><label for="username" class="pt-0 label label-text font-semibold">
                            <span>
                                {{ __('username') }}
                            </span>
                        </label>
                        <div class="flex-1 relative">
                            <input class="input input-primary w-full peer !bg-inherit" type="text"
                                wire:model="username" disabled="disabled">
                        </div>
                    </div>
                    <div><label class="pt-0 label label-text font-semibold">
                            <span>
                                {{ __('email') }}
                            </span>
                        </label>
                        <div class="flex-1 relative">
                            <input class="input input-primary w-full peer !bg-inherit" type="email" wire:model="email"
                                disabled="disabled">
                        </div>
                    </div>
                    <div><label for="phone" class="pt-0 label label-text font-semibold">
                            <span>
                                {{ __('phone') }}
                            </span>
                        </label>
                        <div class="flex-1 relative">
                            <input id="phone" class="input input-primary w-full peer !bg-inherit" type="tel"
                                wire:model="phone" disabled="disabled">
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div id="almrakz-altby" class="mb-10 mb-2">
                    <div class="flex flex-wrap gap-5 justify-between items-center">
                        <div>
                            <div class="text-lg font-extrabold ">
                                {{ __('medical_centers') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3 ">
                            <button type="button" @click="" class="btn normal-case btn-sm btn-circle btn-primary">
                                <span class="block">
                                    <svg class="inline w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                        data-slot="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <ul
                    class="flex flex-wrap text-sm font-medium text-center
                    text-gray-500 border-b border-primary bg-accent p-2">
                    @forelse ($userMedicalCenters as $mc)
                        <li class="me-2" wire:click="chooseCenter({{ $mc->id }})">
                            <p class="inline-block p-2 rounded hover:text-white hover:bg-primary cursor-pointer">
                                {{ $mc->name }}</p>
                        </li>
                    @empty
                    @endforelse
                </ul>

            </div>
        </div>
        {{-- here time container --}}
        <div>
            @isset($centerTimes->workingHours)
                @forelse ($centerTimes->workingHours as $index => $centerTime)
                    @php
                        $openingTime = isset($workingHours[$index]['opening_time'])
                            ? $workingHours[$index]['opening_time']
                            : '';
                        $closingTime = isset($workingHours[$index]['closing_time'])
                            ? $workingHours[$index]['closing_time']
                            : '';
                    @endphp
                    <div class="grid grid-cols-3 space-x-2 mb-1 items-center w-full max-w-md">
                        <div>
                            <label for="day_{{ $index }}" class="flex gap-3 items-center cursor-pointer">
                                <input type="checkbox" id="day_{{ $index }}"
                                    wire:model="selectedDay.{{ $index }}" class="checkbox checkbox-primary">
                                {{ __('week_day_' . $centerTime->day_of_week) }}
                            </label>
                        </div>
                        <div>
                            <label for="start_{{ $index }}" class="pt-0 label label-text font-semibold">
                                <span class="text-gray-500 font-semibold">{{ __('from') }}</span>
                            </label>
                            <div class="flex-1 relative">
                                <input class="input input-primary w-full peer appearance-none h-8 bg-white" type="time"
                                    id="start_{{ $index }}" wire:model="start.{{ $index }}"
                                    value="{{ $openingTime }}" min="{{ $openingTime }}" max="{{ $closingTime }}">
                            </div>
                        </div>
                        <div>
                            <label for="end_{{ $index }}" class="pt-0 label label-text font-semibold">
                                <span class="text-gray-500 font-semibold">{{ __('to') }}</span>
                            </label>
                            <div class="flex-1 relative">
                                <input class="input input-primary w-full peer appearance-none h-8 bg-white" type="time"
                                    id="end_{{ $index }}" wire:model="end.{{ $index }}"
                                    value="{{ $closingTime }}" min="{{ $openingTime }}" max="{{ $closingTime }}">
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No working hours available.</p>
                @endforelse
            @endisset
        </div>
    </form>
</div>
