<div class="w-full p-6 bg-white rounded-xl">
    <x-mary-header title="{{ __('roles') }}"
        subtitle="{{ __('all_roles') }}"
        separator
        progress-indicator>
        <x-slot:middle>
        </x-slot:middle>
        <x-slot name="actions">
            <x-mary-toggle label="{{ __('show_archived_only') }}"
                wire:model.live="showArchived"
                class="focus:bg-primary/10 focus:border-primary focus:outline-primary focus-within:outline-primary"
                right />


            <x-mary-button icon="o-plus"
                class="btn-primary btn-circle"
                @click="$wire.showAddModal">
            </x-mary-button>
        </x-slot>
    </x-mary-header>

    <x-mary-table :headers="$headers"
        :rows="$this->roles()"
        :row-decoration="$this->getRowDecoration()">


        @scope('header_id', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope
        @scope('header_name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_actions', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $role)
            <div class="flex space-x-1">
                @if ($this->showArchived)
                    @can('restore', $role)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_restore',false,{{ $role['id'] }}) "
                            class=" btn-sm btn-circle btn btn-primary
                                   btn-outline bg-accent hover:!text-white" />
                    @endcan
                @else
                    @can('delete', $role)
                        <x-mary-button icon="s-trash"
                            wire:click="changeShowConfirmModal(true, 'are_you_sure_delete', true ,{{ $role['id'] }}) "
                            class=" btn btn-sm  btn-circle
                                   btn-error
                                   btn-outline hover:!text-white bg-accent" />
                    @endcan
                    @canany(['update', 'view'], $role)
                        <x-mary-button icon="s-pencil"
                            wire:click="edit({{ $role['id'] }})"
                            class=" btn-sm btn-circle btn-info
                                   btn-outline hover:!text-white bg-accent" />
                    @endcanany
                @endif
            </div>
        @endscope

    </x-mary-table>


    <x-mary-modal wire:model="addModal"
        subtitle=""
        box-class="border-2 border-primary"
        persistent
        {{--                  title="{{__('add_role')}}" --}}>


        <x-mary-header
            title="{{ $editMode ? __('edit_role') : __('add_role') }}"
            subtitle="{{ $editMode ? '' : __('add_role_subtitle') }}"
            size="text-2xl"
            class="mb-5">
            <x-slot:actions>
                <x-mary-button icon="o-x-mark"
                    @click="$wire.hideModal" />
            </x-slot:actions>
        </x-mary-header>

        <x-mary-form wire:submit="save"
            no-separator>
            <x-mary-input label="{{ __('name') }}"
                wire:model="form.name"
                class="" />
            <p class="mt-6 font-bold test-lg">
                {{ __('permissions') }}
            </p>
            <div
                class="overflow-auto h-[310px] w-full border border-primary
    border-dashed rounded-2xl ">
                @foreach ($permissions as $tableName => $permissionGroup)
                    <x-mary-list-item :item="[$tableName => $permissionGroup]"
                        class="bg-cultured  p-2
                                      hover:!bg-accent">
                        <x-slot:avatar>
                            <p class="w-32 font-bold ">
                                {{ __('' . $tableName) }}
                            </p>
                        </x-slot:avatar>
                        <x-slot:value
                            class="grid grid-cols-2 gap-1">
                            @foreach ($permissionGroup as $permission)
                                {{-- @json($permission) --}}
                                @if ($permission['name'] != 'forceDelete')
                                    <x-mary-checkbox
                                        id="permission_{{ $permission['id'] }}"
                                        wire:model="form.permissions.{{ $permission['id'] }}"
                                        class="">
                                        <x-slot:label>
                                            <span
                                                class="text-xs font-light ">{{ $permission['name'] }}</span>
                                        </x-slot:label>
                                    </x-mary-checkbox>
                                @endif
                            @endforeach
                        </x-slot:value>
                    </x-mary-list-item>
                @endforeach
            </div>

            <x-slot:actions>
                {{--                <x-mary-button label="Cancel" @click="$wire.addModal = false"/> --}}
                <x-mary-button label="Confirm"
                    type="submit"
                    spinner="save"
                    class="w-full mt-3 btn-primary" />
            </x-slot:actions>
        </x-mary-form>

    </x-mary-modal>

    @if ($showConfirmModal)
        <livewire:components.confirm-modal :showModal="$showConfirmModal"
            :message="$confirmMessage"
            :isDelete="$isDelete"
            :key="' ' . now()" />
    @endif
</div>
