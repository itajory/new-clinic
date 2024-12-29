<div class="w-full p-6 bg-white">
    <x-mary-header title="{{ __('doctors') }}"
        subtitle="{{ __('all_doctors') }}"
        separator
        progress-indicator>
        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="users"
                class="border-primary focus:outline-primary">
            </x-mary-input>
        </x-slot:middle>
        <x-slot name="actions">
            <x-mary-button icon="o-funnel"
                class="relative  btn-circle"
                @click="$wire.showFilterDrawer = true">
                @if ($this->filtersCount() > 0)
                    <x-mary-badge value="{{ $this->filtersCount() }}"
                        class="absolute badge-warning -right-2 -top-2" />
                @endif
            </x-mary-button>
            @can('create', User::class)
                <x-mary-button icon="o-plus"
                    class="btn-primary btn-circle "
                    @click="">
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
        @scope('header_medicalCenters', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('actions', $user)
            <div class="flex space-x-1">

                @if ($this->showArchived)
                    @can('restore', $user)
                        <x-mary-button icon="c-arrow-up-tray"
                            wire:click="restore({{ $user['id'] }})"
                            wire:confirm="{{ __('are_you_sure_restore') }}"
                            class="rounded-full  btn-sm btn-circle text-secondary bg-secondary/10 border-secondary hover:bg-secondary hover:text-white" />
                    @endcan
                @else
                    {{--                @can('delete', $user) --}}
                    {{--                    <x-mary-button icon="s-trash" --}}
                    {{--                                   wire:click="delete({{$user['id']}})" --}}
                    {{--                                   wire:confirm="{{__('are_you_sure_delete')}}" --}}
                    {{--                                   class=" btn-sm btn-circle text-myred bg-myred/10 --}}
                    {{--                       border-myred --}}
                    {{--                       hover:bg-myred hover:text-white rounded-full --}}
                    {{--                       "/> --}}
                    {{--                @endcan --}}

                    @canany(['view'], $user)
                        <x-mary-button icon="s-eye"
                            link="{{ route('doctors.view', $user['id']) }}"
                            class="btn-sm btn-circle btn-info
                                   btn-outline bg-accent hover:!text-white" />
                        {{--                                    <x-mary-button icon="c-lock-closed" --}}
                        {{--                                                   wire:click="" --}}
                        {{--                                                   class=" btn-sm btn-circle text-warning --}}
                        {{--                                                   bg-warning/10 --}}
                        {{--                                       border-warning --}}
                        {{--                                       hover:bg-warning hover:text-white rounded-full "/> --}}
                    @endcanany
                @endif
            </div>
        @endscope

        @scope('cell_medicalCenters', $user)
            <div class="space-x-0.5">
                @foreach ($user->medicalCenters as $medicalCenter)
                    @if ($loop->index > 0)
                        <x-mary-badge :value="$medicalCenter->name"
                            class="badge bg-myblue/50" />
                    @else
                        <x-mary-badge :value="$medicalCenter->name"
                            class="badge bg-primary/50" />
                    @endif
                @endforeach
            </div>
        @endscope


    </x-mary-table>
</div>
