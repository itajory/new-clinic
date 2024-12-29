<div
    class="relative h-full pb-16 overflow-x-auto overflow-y-auto shadow-md sm:rounded-lg hide-scrollbar">
    @if ($records === null || $records->count() === 0)
        <div class="p-6">
            <p>{{ __('no_history') }}</p>
        </div>
    @else
        <table
            class="relative w-full text-sm text-left text-gray-500 rtl:text-right dark:text-gray-400">
            <thead
                class="sticky top-0 left-0 right-0 text-xs text-gray-700 uppercase border-b border-gray-500 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                <tr>


                    @foreach ($headers as $key => $header)
                        <th scope="col"
                            class="px-3 py-2 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">

                            {{ $header['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 ">
                @foreach ($records as $item)
                    <tr class=" hover:bg-gray-50">



                        @foreach ($headers as $key => $value)
                            <td class="px-3 py-2 whitespace-nowrap">

                                @if (
                                    $value['key'] === 'treatment' ||
                                        $value['key'] === 'doctor' ||
                                        $value['key'] === 'medicalCenter')
                                    {{ $item->{$value['key']}->name }}
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
