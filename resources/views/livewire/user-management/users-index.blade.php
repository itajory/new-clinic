@php use App\Models\User; @endphp
<div class="w-full p-6 bg-white">
    <x-mary-header title="{{ __('users') }}"
        subtitle="{{ __('all_users') }}"
        separator
        progress-indicator>
        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="users"
                class="">
            </x-mary-input>
        </x-slot:middle>
        <x-slot name="actions">
            <x-mary-button icon="o-funnel"
                class="relative btn-circle"
                @click="$wire.showFilterDrawer = true">
                @if ($this->filtersCount() > 0)
                    <x-mary-badge value="{{ $this->filtersCount() }}"
                        class="absolute badge-warning -right-2 -top-2" />
                @endif
            </x-mary-button>
            @can('create', User::class)
                <x-mary-button icon="o-plus"
                    class="btn-primary btn-circle "
                    @click="$wire.showAddModal">
                </x-mary-button>
            @endcan

        </x-slot>
    </x-mary-header>

    <x-mary-drawer wire:model="showFilterDrawer"
        wire:ignore.self
        class="w-11/12 lg:w-1/3 "
        title="{{ __('filter') }}"
        with-close-button
        right
        separator>
        <div class="space-y-2">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord" />

            <x-mary-select label="{{ __('roles') }}"
                :options="$roles"
                {{-- icon="c-briefcase" --}}
                placeholder="{{ __('select_role') }}"
                placeholder-value="0"
                wire:model.live="roleId" />

            <x-mary-select label="{{ __('medical_centers') }}"
                :options="$medicalCenters"
                {{-- icon="c-briefcase" --}}
                placeholder="{{ __('select_medical_center') }}"
                placeholder-value="0"
                wire:model.live="medicalCenterId" />
            <x-mary-toggle label="{{ __('show_archived_only') }}"
                wire:model.live="showArchived"
                class="focus:bg-primary/10 focus:border-primary focus:outline-primary focus-within:outline-primary "
                right
                tight />
        </div>
        <x-slot:actions>
            @if ($this->filtersCount() > 0)
                <x-mary-button label="{{ __('reset') }}"
                    wire:click="clearFilters"
                    class="btn-warning " />
            @endif
            <x-mary-button label="{{ __('done') }}"
                @click="$wire.showFilterDrawer = false"
                class="btn-primary " />
        </x-slot:actions>
    </x-mary-drawer>


    {{--    @if ($users->count() > 0) --}}
    <x-mary-table :headers="$headers"
        :rows="$this->users()"
        with-pagination
        per-page="perPage"
        :sort-by="$sortBy"
        :row-decoration="$this->getRowDecoration()"
        class="[&_th>*]:!text-black [&_th>*]:!inline-flex
                  [&_th>*]:!font-bold "
        :per-page-values="$perPageOptions"
        show-empty-text
        empty-text="{{ __('no_data_found') }}">
        @scope('header_id', $header)
            <p class="font-bold text-black ">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_email', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_username', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_role.name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $user)
            <div class="flex space-x-1">

                @if ($this->showArchived)
                    @can('restore', $user)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $user['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                @if ($user->role_id != 1)
                    @can('delete', $user)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true, {{ $user['id'] }}) "
                            class=" btn btn-sm btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent
                       " />
                    @endcan

                @endif

                    @canany(['update', 'view'], $user)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit({{ $user['id'] }})"
                            class=" btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent" />
                        <x-mary-button icon="c-lock-closed"
                            wire:click="changePassword({{ $user['id'] }})"
                            class=" btn-sm btn-circle
                                   btn-warning
                                   btn-outline hover:!text-white bg-accent " />
                    @endcanany
                @endif
            </div>
        @endscope

    </x-mary-table>
    {{--    @else --}}
    {{--        <livewire:components.no-data/> --}}
    {{--    @endif --}}


    <x-mary-modal wire:model="addModal"
        subtitle=""
        box-class="border-2 border-primary "
        persistent>


        <x-mary-header
            title="{{ $editMode ? __('edit_user') : __('add_user') }}"
            subtitle="{{ $editMode ? '' : __('add_user_subtitle') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideAddModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <div class="grid grid-cols-2 gap-3 space-x-1">
                <x-mary-input label="{{ __('name') }}"
                    wire:model="form.name"
                    class=""
                    type="text"
                    :disabled="$this->disableInputsWhenEditMode()" />
                <x-mary-input label="{{ __('username') }}"
                    wire:model="form.username"
                    class=""
                    type="text"
                    :disabled="$this->disableInputsWhenEditMode()" />
                <x-mary-input label="{{ __('email') }}"
                    wire:model="form.email"
                    class=""
                    type="email"
                    :disabled="$this->disableInputsWhenEditMode()" />
                @if (!$editMode)
                    <x-mary-input label="{{ __('password') }}"
                        wire:model="form.password"
                        class=""
                        type="password"
                        :disabled="$this->disableInputsWhenEditMode()" />
                @endif
                <x-mary-input label="{{ __('phone') }}"
                    wire:model="form.phone"
                    class=""
                    type="tel"
                    :disabled="$this->disableInputsWhenEditMode()" />
                <x-mary-select :options="$roles"
                    {{-- icon="c-briefcase" --}}
                    placeholder="{{ __('select_role') }}"
                    placeholder-value="0"
                    wire:model.live="form.role_id"
                    class=""
                    :disabled="$this->disableInputsWhenEditMode()">
                    <x-slot:label>
                        <span class="font-semibold text-gray-500">
                            {{ __('role') }}</span>
                    </x-slot:label>
                </x-mary-select>
                @if ($this->form->role_id == 2)
                    <x-mary-select :options="$treatments"
                        {{-- icon="c-briefcase" --}}
                        placeholder="{{ __('select_treatment') }}"
                        placeholder-value="0"
                        wire:model="form.treatment_id"
                        class=""
                        :disabled="$this->disableInputsWhenEditMode()">
                        <x-slot:label>
                            <span class="font-semibold text-gray-500">
                                {{ __('treatment') }}</span>
                        </x-slot:label>
                    </x-mary-select>
                @endif

            </div>
            <x-mary-header title="{{ __('select_medical_centers') }}"
                subtitle="{{ __('select_multiple') }}"
                size="text-lg"
                class="mb-2" />
            <ul
                class="grid items-center justify-start grid-cols-2 overflow-auto md:grid-cols-3 max-h-64 ">
                @foreach ($medicalCenters as $medicalCenter)
                    <li class="m-1">
                        <input type="checkbox"
                            id="{{ $medicalCenter->id }}"
                            name="medicalCenters"
                            class="hidden peer"
                            wire:model.live.number="form.medicalCenters"
                            value="{{ $medicalCenter->id }}"
                            @if ($this->disableInputsWhenEditMode()) disabled @endif />
                        <label for="{{ $medicalCenter->id }}"
                            class="inline-flex items-center justify-center w-full h-10 p-1 py-2 border cursor-pointer rounded-2xl border-primary peer-checked:border-primary peer-checked:text-primary peer-checked:bg-accent hover:text-primary hover:bg-accent min-w-36">
                            <p class="text-xs text-center">
                                {{ $medicalCenter->name }}</p>
                        </label>
                    </li>
                @endforeach
                @error('form.medicalCenters')
                    <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </ul>


            @if ($editMode)
                @can('update', $form->user)
                    <x-slot:actions>
                        <x-mary-button label="{{ __('confirm') }}"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn btn-primary" />
                    </x-slot:actions>
                @endcan
            @else
                @can('create', User::class)
                    <x-slot:actions>
                        <x-mary-button label="{{ __('confirm') }}"
                            type="submit"
                            spinner="save"
                            class="w-full mt-3 btn btn-primary" />
                    </x-slot:actions>
                @endcan
            @endif
        </x-mary-form>


    </x-mary-modal>


    <x-mary-modal wire:model="showChangePasswordModal"
        subtitle=""
        box-class="border-2 border-primary "
        persistent>


        <x-mary-header title="{{ __('change_password') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideChanhgePassword" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <x-mary-input label="{{ __('password') }}"
                wire:model="form.password"
                class=""
                id="changePassword"
                type="password" />
            @can('update', $form->user)
                <x-slot:actions>
                    <x-mary-button label="Confirm"
                        type="submit"
                        spinner="save"
                        class="w-full mt-3 btn btn-primary" />
                </x-slot:actions>
            @endcan
        </x-mary-form>


    </x-mary-modal>

    @if ($showConfirmModal)
        <livewire:components.confirm-modal :showModal="$showConfirmModal"
            :message="$confirmMessage"
            :isDelete="$isDelete"
            :key="' ' . now()" />
    @endif
</div>
