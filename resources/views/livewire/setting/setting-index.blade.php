<div class="w-full p-6 bg-white rounded-xl"
    x-data="{ showEdit: false }"
    x-on:settings-saved.window="showEdit = false">
    <x-mary-header title="{{ __('Settings') }}"
        separator
        progress-indicator>

        <x-slot:middle
            class="!justify-end ">

        </x-slot:middle>

        <x-slot name="actions">

            <x-mary-button icon="o-pencil"
                x-show="!showEdit"
                @click="showEdit=true"
                class="btn-info "
                type="">
                {{ __('edit') }}
            </x-mary-button>

            <x-mary-button icon="c-x-mark"
                x-show="showEdit"
                class=" btn-error"
                @click="showEdit=false">
                {{ __('Cancel') }}
            </x-mary-button>

            <x-mary-button icon="o-check"
                x-show="showEdit"
                class="btn-primary "
                type="submit"
                wire:click="save">
                {{ __('Save') }}
            </x-mary-button>
        </x-slot>
    </x-mary-header>

    <div class="mt06">

        <x-mary-form wire:submit="save"
            no-separator
            class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">

            @foreach ($this->form->settings as $index => $setting)
                @if ($setting['key'] == 'languages')
                    <div class="col-span-1">
                        {{-- <label
                            class="block mb-2 font-semibold ms-1">{{ __($setting['key']) }}</label>
                        <div class="flex items-center gap-4">
                            @foreach (['ar', 'en', 'he'] as $lang)
                                <label class="flex items-center ms-2">
                                    <input type="checkbox"
                                        wire:model="selectedLanguages"
                                        value="{{ $lang }}"
                                        :disabled="!showEdit"
                                        class="mr-2 checkbox checkbox-primary"
                                        {{ in_array($lang, $selectedLanguages) ? 'checked' : '' }}>
                                    {{ strtoupper($lang) }}
                                </label>
                            @endforeach
                        </div> --}}
                    </div>
                @elseif ($setting['key'] == 'logo')
                    <div class="col-span-1 ">
                        <label
                            class="block mb-2 font-semibold ms-1">{{ __($setting['key']) }}</label>

                        <input type="file"
                            x-show="showEdit"
                            wire:model="logoFile"
                            :disabled="!showEdit"
                            class="w-full file-input file-input-bordered"
                            accept="image/*">
                        @if ($logoPreview)
                            <img src="{{ $logoPreview }}"
                                alt="Logo Preview"
                                class="max-w-xs mt-2">
                        @elseif ($setting['value'])
                            <img src="{{ asset('storage/' . $setting['value']) }}"
                                alt="Current Logo"
                                class="mt-2 ">
                        @endif
                    </div>
                @else
                    <x-mary-input label="{{ __($setting['key']) }}"
                        wire:model="form.settings.{{ $index }}.value"
                        class="col-span-1"
                        x-bind:disabled="!showEdit" />
                @endif
            @endforeach

        </x-mary-form>
    </div>





</div>
