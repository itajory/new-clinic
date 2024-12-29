<div
    class="relative h-full pb-16 overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg hide-scrollbar">
    @if ($checks === null || $checks->count() === 0)
        <div class="p-6">
            <p>{{ __('no_checks') }}</p>
        </div>
    @else
        <table
            class="relative w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
            <thead
                class="sticky top-0 left-0 right-0 text-xs text-gray-700 uppercase border-b border-gray-500 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                <tr>

                    {{-- @if ($activeTab == 'not_paid') --}}
                    {{-- <th scope="col"
                        class="px-3 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                        <input type="checkbox"
                            class="checkbox checkbox-sm checkbox-primary"
                            wire:model.live="selectAll">
                    </th> --}}
                    {{-- @endif --}}

                    @foreach ($headers as $key => $header)
                        <th scope="col"
                            class="px-3 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">

                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 ">
                @foreach ($checks as $item)
                    <tr
                        class="{{ in_array($item['id'], $selectedRows) ? 'bg-gray-100' : '' }} hover:bg-gray-50">

                        {{-- @if ($activeTab == 'not_paid') --}}
                        {{-- <td class="px-3 py-2 whitespace-nowrap">
                            <input type="checkbox"
                                class="checkbox checkbox-sm checkbox-primary"
                                wire:model.live="selectedRows"
                                value="{{ $item->id }}">
                        </td> --}}
                        {{-- @endif --}}

                        @foreach ($headers as $key => $value)
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if ($value['key'] === 'bank')
                                    {{ $item->{$value['key']}->name }}
                                @elseif($value['key'] === 'user')
                                    {{ $item->{$value['key']}->name }}
                                @elseif($value['key'] === 'status')
                                    @if ($item->{$value['key']} === 'returned')
                                        <span class='badge badge-error'>
                                            {{ __($item->{$value['key']}) }}</span>
                                    @elseif ($item->{$value['key']} === 'collected')
                                        <span class='badge badge-success'>
                                            {{ __($item->{$value['key']}) }}</span>
                                    @elseif ($item->{$value['key']} === 'replaced_with_check')
                                        <span class='badge badge-info'>
                                            {{ __($item->{$value['key']}) }}</span>
                                    @else
                                        <span class='badge badge-natural'>
                                            {{ __($item->{$value['key']}) }}</span>
                                    @endif
                                @elseif($value['key'] === 'replacement')
                                    @if ($item->{$value['key']} != null)
                                        {{ __('check') . ' ' . $item->{$value['key']}->check_number }}
                                    @endif
                                @elseif($value['key'] === 'actions')
                                    @if ($item->status == 'pending')
                                        <x-mary-button icon="c-arrow-path"
                                            class=" btn-sm btn-circle
                                btn-info
                                btn-outline hover:!text-white bg-accent "
                                            wire:click="changeShowChecKModal({{ true }}, {{ $item }})" />
                                    @endif
                                @else
                                    {{ $item->{$value['key']} }}
                                @endif
                            </td>
                        @endforeach

                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
