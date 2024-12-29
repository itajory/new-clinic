<div class="w-full p-6 bg-white">
    <x-mary-header title="{{ __('System Logs') }}"
        subtitle="{{ __('All System Logs') }}"
        separator
        progress-indicator>
        <x-slot:middle
            class="!justify-end ">
            <x-mary-input placeholder="{{ __('search') }}"
                wire:model.blur="searchWord"
                wire:keydown.enter="systemLogs"
                class="">
            </x-mary-input>
        </x-slot:middle>
        <x-slot name="actions">

        </x-slot>
    </x-mary-header>



    <x-mary-table :headers="$headers"
        :rows="$this->systemLogs()"
        with-pagination
        per-page="perPage"
        :sort-by="$sortBy"
        :row-decoration="$this->getRowDecoration()"
        class="[&_th>*]:!text-black [&_th>*]:!inline-flex
        [&_th>*]:!font-bold "
        :per-page-values="$perPageOptions"
        show-empty-text
        empty-text="{{ __('no_data_found') }}">
        @scope('header_created_at', $header)
            <p class="font-bold text-black ">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_user.name', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_email', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_event_description', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope

        @scope('header_table_id', $header)
            <p class="font-bold text-black">
                {{ $header['label'] }}
            </p>
        @endscope



    </x-mary-table>


</div>
