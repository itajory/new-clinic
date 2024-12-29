<div
    class="relative h-full pb-16 overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg hide-scrollbar">
    @if ($appointments === null || $appointments->count() === 0)
        <div class="p-6">
            <p>{{ __('no_data') }}</p>
        </div>
    @else
        <table
            class="relative w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
            <thead
                class="sticky top-0 left-0 right-0 text-xs text-gray-700 uppercase border-b border-gray-500 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                <tr>

                    @if (
                        $activeTab == 'not_paid' ||
                            $activeTab == 'paid' ||
                            $activeTab == 'not_closed')
                        <th scope="col"
                            class="px-3 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                            <input type="checkbox"
                                class="checkbox checkbox-sm checkbox-primary"
                                wire:model.live="selectAll">
                        </th>
                    @endif

                    @foreach ($headers as $key => $header)
                        <th scope="col"
                            class="px-3 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">

                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 ">
                @foreach ($appointments as $item)
                    <tr
                        class="{{ in_array($item['id'], $selectedRows) ? 'bg-gray-100' : '' }} hover:bg-gray-50">

                        @if (
                            $activeTab == 'not_paid' ||
                                $activeTab == 'paid' ||
                                $activeTab == 'not_closed')
                            <td class="px-3 py-2 whitespace-nowrap">
                                <input type="checkbox"
                                    class="checkbox checkbox-sm checkbox-primary"
                                    wire:model.live="selectedRows"
                                    value="{{ $item->id }}">
                            </td>
                        @endif

                        @foreach ($headers as $key => $value)
                            <td class="px-3 py-2 whitespace-nowrap">

                                @if ($value['key'] === 'appointment_time')
                                    {{ substr($item->{$value['key']}, 0, 10) }}
                                @elseif($value['key'] === 'status')
                                    {{ __($item->{$value['key']}) }}
                                @elseif($value['key'] === 'patient_fund_id')
                                    {{ $item->patientFund?->name }}
                                @elseif($value['key'] === 'actions')
                                    @if ($parentView === 'patient')
                                        <x-mary-button icon="c-eye"
                                            wire:click="showAppointment({{ $item }})"
                                            class=" btn-sm btn-circle
                            btn-primary
                            btn-outline hover:!text-white bg-accent " />
                                    @endif
                                @elseif($value['key'] === 'payments')
                                    @if ($item->{$value['key']}->isNotEmpty())
                                        {{ $item->{$value['key']}[0]->payment_type }}
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




    <livewire:components.patietn-appointment-view :$viewAppointment
        :$showAppointmentModal
        :key="$viewAppointment ? $viewAppointment['id'] : null" />
</div>
