<div class="w-full p-6 text-sm bg-white rounded-lg shadow-md">
    {{--    Header --}}
    <x-mary-header subtitle=""
                   separator
                   progress-indicator="{{ !$isNested }}">
        <x-slot name="title">
            {{ $patient->full_name }}
        </x-slot>

        <x-slot name="actions">
            @if (!$isNested)
                <x-mary-button icon="o-pencil"
                               class="btn-info "
                               type=""
                               link="{{ route('patient.update', $patient->id) }}"
                               wire:navigate>
                    {{ __('edit') }}
                </x-mary-button>
            @endif

        </x-slot>
    </x-mary-header>

    <div class="w-full ">
        <div
                class="grid grid-cols-1 gap-1 p-4 mb-6 bg-cultured md:grid-cols-12 md:gap-2">
            <dvi class="col-span-12 md:col-span-8">

                <div class="grid grid-cols-1 gap-1 md:grid-cols-12 ">

                    <div class="font-semibold md:col-span-2">
                        {{ __('id_number') }}</div>
                    <div class="md:col-span-4 md:ps-4">
                        {{ $patient->id_number }}</div>

                    <div class="font-semibold md:col-span-2">
                        {{ __('gender') }}</div>
                    <div class="md:col-span-4 md:ps-4">{{ $patient->gender }}
                    </div>

                    <div class="font-semibold md:col-span-2">
                        {{ __('birth_date') }}</div>
                    <div class="md:col-span-4 md:ps-4">
                        {{ $patient->birth_date }}</div>

                    <div class="font-semibold md:col-span-2">
                        {{ __('guardian_phone') }}</div>
                    <div class="md:col-span-4 md:ps-4">
                        {{ $patient->guardian_phone }}</div>

                    <div class="font-semibold md:col-span-2">
                        {{ __('patient_phone') }}</div>
                    <div class="md:col-span-4 md:ps-4">
                        {{ $patient->patient_phone }}</div>

                    <div class="font-semibold md:col-span-2">
                        {{ __('city') }}</div>
                    <div class="md:col-span-4 md:ps-4">
                        {{ $patient->city->name }}</div>

                </div>
            </dvi>

            @can('viewAny', \App\Models\Payment::class)
                <div class="col-span-12 md:col-span-4">

                    <div
                            class="grid grid-cols-1 gap-1 md:grid-cols-12 md-col-span-6 md:gap-3 ">

                        <div class="font-semibold md:col-span-3">
                            {{ __('Not Paid') }}</div>
                        <div class="md:col-span-9 md:ps-4">{{ $notPaidCost }}
                        </div>

                        <div class="font-semibold md:col-span-3">
                            {{ __('Returned Checks') }}</div>
                        <div class="md:col-span-9 md:ps-4">
                            {{ $returnedChecksCount }}
                        </div>

                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="md:flex">
        <ul
                class="mb-4 space-y-4 text-sm font-medium text-gray-500 flex-column space-y md:me-4 md:mb-0">
            @foreach ($tabs as $key => $tab)
                <li>
                    @if ($key == 'finanical_report')
                        @can('viewAny', \App\Models\Payment::class)
                            <button type="button"
                                    class="inline-flex items-center px-4 py-3
                                border-s-4 border-cultured group
                       hover:text-black hover:font-bold w-full hover:border-primary
                         cursor-pointer {{ $activeTab == $key ? ' text-black font-bold border-primary ' : '' }}"
                                    wire:click.prevent="setActiveTab('{{ $key }}')">
                                <svg class="w-4 h-4 me-2  group-hover:text-black  {{ $activeTab == $key ? ' text-black ' : ' text-gray-500 ' }}"
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="24"
                                     height="24"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    @php echo $tab['icon']; @endphp
                                </svg>
                                {{ __($tab['title']) }}
                            </button>
                        @endcan
                    @else
                        <button type="button"
                                class="inline-flex items-center px-4 py-3
                                border-s-4 border-cultured group
                       hover:text-black hover:font-bold w-full hover:border-primary
                         cursor-pointer {{ $activeTab == $key ? ' text-black font-bold border-primary ' : '' }}"
                                wire:click.prevent="setActiveTab('{{ $key }}')">
                            <svg class="w-4 h-4 me-2  group-hover:text-black  {{ $activeTab == $key ? ' text-black ' : ' text-gray-500 ' }}"
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="24"
                                 height="24"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                @php echo $tab['icon']; @endphp
                            </svg>
                            {{ __($tab['title']) }}
                        </button>
                    @endif
                </li>
            @endforeach


        </ul>
        <div
                class="w-full h-[calc(100vh-400px)] border-2 border-cultured overflow-hidden">
            @if ($activeTab == 'finanical_report')
                {{-- <div class="flex flex-wrap items-start justify-start gap-2 p-4"> --}}
                <livewire:components.patient-financial-report :$patient
                                                              :key="$patient->id . $activeTab"/>
                {{-- </div> --}}
            @endif

            @if ($activeTab == 'history')
                {{-- <div class="flex flex-wrap items-start justify-start gap-2 p-4"> --}}
                <livewire:patient-record.view-patient-record
                        :patientId="$patient->id"
                        :key="$patient->id . $activeTab"/>
                {{-- </div> --}}
            @endif

            @if ($activeTab == 'docs')

                <div
                        class="relative grid w-full h-full grid-cols-1 md:grid-cols-12 ">
                    <div class="absolute z-10 bottom-3 end-3 mymenu">
                        <x-mary-dropdown class="bg-accent">
                            <x-slot:trigger>
                                <x-mary-button icon="m-plus"
                                               class="btn-circle btn-outline btn-primary"/>
                            </x-slot:trigger>
                            <x-mary-menu-item title="{{ __('folder') }}"
                                              wire:click="setShowAddFileModal(true, 'folder' )"/>
                            <x-mary-menu-item title="{{ __('document') }}"
                                              wire:click="setShowAddFileModal(true, 'file' )"/>

                        </x-mary-dropdown>
                    </div>
                    {{--                        folders tree --}}
                    <div
                            class="hidden h-full p-4 md:block md:col-span-3 lg:col-span-2 border-e-2 border-cultured">
                        <div class="cursor-pointer"
                             wire:click="setCurrentFolder({{ 0 }})">
                            <x-mary-timeline-item title="{{ __('root') }}"
                                                  first
                                                  icon="o-folder-open"/>
                        </div>
                        @foreach ($this->getFoldersTree() as $folder)
                            <div class="cursor-pointer"
                                 wire:click="setCurrentFolder({{ $folder->id }})">
                                <x-mary-timeline-item
                                        title="{{ $folder->title }}"
                                        icon="o-folder-open"
                                        class="cursor-pointer"/>
                            </div>
                        @endforeach


                    </div>
                    <div
                            class="flex flex-col col-span-12 md:col-span-9 lg:col-span-10 ">
                        {{--                            header --}}

                        <div
                                class="flex items-center justify-start w-full h-10 px-4 bg-cultured">
                            @if ($this->currentFolderObj)
                                <x-mary-icon name="o-arrow-left"
                                             class="cursor-pointer me-4"
                                             wire:click.prevent="goBackFiles()"/>
                            @endif
                            {{ $this->currentFolderObj ? $this->currentFolderObj->title : __('root') }}
                        </div>
                        {{--                            body --}}
                        <div
                                class="flex flex-wrap items-start justify-start gap-2 p-4">
                            {{--                                items --}}
                            @foreach ($this->getFolderChildren() as $child)
                                @if ($child->path == null)
                                    <div
                                            class="relative flex flex-col items-center justify-start w-32 h-32 p-3 border rounded cursor-pointer group hover:border-primary hover:bg-accent">

                                        <div
                                                class="top-0 bottom-0 left-0
                                                right-0  items-center
                                                justify-evenly hidden w-28 h-28
                                                absolut bg-accent group-hover:flex border-r-primary border-l-primary">
                                            <x-mary-icon name="c-eye"
                                                         class="flex-1 w-8 h-8 group-hover:text-primary"
                                                         wire:click="setCurrentFolder({{ $child->id }})"/>

                                            <x-mary-icon name="c-pencil"
                                                         class="flex-1 w-8 h-8 group-hover:text-info"
                                                         wire:click="setShowAddFileModal({{true}}, 'folder', {{true}}, {{ $child->id }})"
                                            />
                                            <x-mary-icon name="s-trash"
                                                         class="flex-1 w-8
                                                         h-8
                                                         group-hover:text-error"
                                                         wire:click="changeShowConfirmModal({{true}}, 'are_you_sure_delete', {{true}}, {{ $child->id }}) "
                                            />

                                        </div>
                                        <x-mary-icon name="o-folder"
                                                     class="flex-1 w-20 h-20 group-hover:text-primary"/>
                                        <p>{{ $child->title }}</p>
                                    </div>
                                @else
                                    {{--                                items --}}
                                    <div
                                            class="relative flex flex-col items-center justify-start w-32 h-32 p-3 border rounded cursor-pointer hover:bg-accent hover:border-primary group">
                                        <div
                                                class="top-0 bottom-0 left-0
                                                right-0  items-center
                                                justify-evenly hidden w-28 h-28
                                                absolut bg-accent group-hover:flex border-r-primary border-l-primary">
                                            <x-mary-icon
                                                    name="c-arrow-down-tray"
                                                    class="flex-1 w-8 h-8 group-hover:text-primary"
                                                    wire:click="downloadDoc({{ $child }})"/>
                                            <x-mary-icon name="c-pencil"
                                                         class="flex-1 w-8 h-8 group-hover:text-info"
                                                         wire:click="setShowAddFileModal({{true}}, 'folder', {{true}}, {{ $child->id }})"
                                            />
                                            <x-mary-icon name="s-trash"
                                                         class="flex-1 w-8
                                                         h-8
                                                         group-hover:text-error"
                                                         wire:click="changeShowConfirmModal({{true}}, 'are_you_sure_delete', {{true}}, {{ $child->id }}) "
                                            />

                                        </div>
                                        <x-mary-icon name="o-document"
                                                     class="flex-1 w-20 h-20 group-hover:text-primary"/>
                                        <p>{{ $child->title }}</p>
                                    </div>
                                @endif
                            @endforeach


                        </div>
                    </div>
                </div>
            @endif
            @if ($activeTab == 'patient_funds')
                <div
                        class="flex flex-wrap items-start justify-start gap-2 p-4">
                    @foreach ($patient->patientFunds as $fund)
                        <x-mary-card title="{{ $fund->name }}"
                                     subtitle="{{ $fund->contribution_type }}"
                                     shadow
                                     separator
                                     class="bg-cultured hover:bg-accent">
                            {{ $fund->pivot->contribution_percentage }}
                        </x-mary-card>
                    @endforeach
                </div>

            @endif
        </div>
    </div>


    {{--        --}}




    {{--    modals --}}
    @if ($activeTab == 'docs')
        <x-mary-modal wire:model="showAddFileModal"
                      subtitle=""
                      box-class="border-2 border-primary"
                      persistent>
            <x-mary-header title="{{ __('add_document') }}"
                           size="text-2xl"
                           class="mb-5">
                <x-slot:actions>
                    <x-mary-button icon="o-x-mark"
                                   wire:click.prevent="setShowAddFileModal(false, '')"/>
                </x-slot:actions>
            </x-mary-header>

            <x-mary-form wire:submit="saveFileFolder"
                         no-separator>
                <x-mary-input
                        label="{{ $documentType == 'file' ? __('document_title') : __('folder_title') }}"
                        wire:model="addFileFolderForm.title"
                        class=""/>
                @if ($documentType == 'file')
                    <x-mary-file wire:model="addFileFolderForm.path"
                                 label="{{ __('document') }}"
                                 accept="application/pdf, image/png, image/jpeg"/>
                @endif
                <x-slot:actions>
                    {{--                <x-mary-button label="Cancel" @click="$wire.addModal = false"/> --}}
                    <x-mary-button label="{{ __('Confirm') }}"
                                   type="submit"
                                   spinner="saveFileFolder"
                                   class="w-full mt-3 btn-primary"/>
                </x-slot:actions>
            </x-mary-form>

        </x-mary-modal>
    @endif

    @if ($showConfirmModal)
        <livewire:components.confirm-modal :showModal="$showConfirmModal"
                                           :message="$confirmMessage"
                                           :isDelete="$isDelete"
                                           :key="' ' . now()"/>
    @endif
</div>
