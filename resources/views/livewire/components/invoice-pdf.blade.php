@php
    // use Illuminate\Support\Facades\Storage;
    use App\Models\Setting;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Professional Invoice</title>
    <style>
        *,
        ::before,
        ::after {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
            --tw-contain-size: ;
            --tw-contain-layout: ;
            --tw-contain-paint: ;
            --tw-contain-style: ;
        }

        ::backdrop {
            --tw-border-spacing-x: 0;
            --tw-border-spacing-y: 0;
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-rotate: 0;
            --tw-skew-x: 0;
            --tw-skew-y: 0;
            --tw-scale-x: 1;
            --tw-scale-y: 1;
            --tw-pan-x: ;
            --tw-pan-y: ;
            --tw-pinch-zoom: ;
            --tw-scroll-snap-strictness: proximity;
            --tw-gradient-from-position: ;
            --tw-gradient-via-position: ;
            --tw-gradient-to-position: ;
            --tw-ordinal: ;
            --tw-slashed-zero: ;
            --tw-numeric-figure: ;
            --tw-numeric-spacing: ;
            --tw-numeric-fraction: ;
            --tw-ring-inset: ;
            --tw-ring-offset-width: 0px;
            --tw-ring-offset-color: #fff;
            --tw-ring-color: rgb(59 130 246 / 0.5);
            --tw-ring-offset-shadow: 0 0 #0000;
            --tw-ring-shadow: 0 0 #0000;
            --tw-shadow: 0 0 #0000;
            --tw-shadow-colored: 0 0 #0000;
            --tw-blur: ;
            --tw-brightness: ;
            --tw-contrast: ;
            --tw-grayscale: ;
            --tw-hue-rotate: ;
            --tw-invert: ;
            --tw-saturate: ;
            --tw-sepia: ;
            --tw-drop-shadow: ;
            --tw-backdrop-blur: ;
            --tw-backdrop-brightness: ;
            --tw-backdrop-contrast: ;
            --tw-backdrop-grayscale: ;
            --tw-backdrop-hue-rotate: ;
            --tw-backdrop-invert: ;
            --tw-backdrop-opacity: ;
            --tw-backdrop-saturate: ;
            --tw-backdrop-sepia: ;
            --tw-contain-size: ;
            --tw-contain-layout: ;
            --tw-contain-paint: ;
            --tw-contain-style: ;
        }

        /*
! tailwindcss v3.4.13 | MIT License | https://tailwindcss.com
*/
        /*
1. Prevent padding and border from affecting element width. (https://github.com/mozdevs/cssremedy/issues/4)
2. Allow adding a border to an element by just adding a border-width. (https://github.com/tailwindcss/tailwindcss/pull/116)
*/
        *,
        ::before,
        ::after {
            box-sizing: border-box;
            /* 1 */
            border-width: 0;
            /* 2 */
            border-style: solid;
            /* 2 */
            border-color: #e5e7eb;
            /* 2 */
        }

        ::before,
        ::after {
            --tw-content: '';
        }

        /*
1. Use a consistent sensible line-height in all browsers.
2. Prevent adjustments of font size after orientation changes in iOS.
3. Use a more readable tab size.
4. Use the user's configured `sans` font-family by default.
5. Use the user's configured `sans` font-feature-settings by default.
6. Use the user's configured `sans` font-variation-settings by default.
7. Disable tap highlights on iOS
*/
        html,
        :host {
            line-height: 1.5;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
            -moz-tab-size: 4;
            /* 3 */
            tab-size: 4;
            /* 3 */
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            /* 4 */
            font-feature-settings: normal;
            /* 5 */
            font-variation-settings: normal;
            /* 6 */
            -webkit-tap-highlight-color: transparent;
            /* 7 */
        }

        /*
1. Remove the margin in all browsers.
2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
*/
        body {
            margin: 0;
            /* 1 */
            line-height: inherit;
            /* 2 */
        }

        /*
1. Add the correct height in Firefox.
2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
3. Ensure horizontal rules are visible by default.
*/
        hr {
            height: 0;
            /* 1 */
            color: inherit;
            /* 2 */
            border-top-width: 1px;
            /* 3 */
        }

        /*
Add the correct text decoration in Chrome, Edge, and Safari.
*/
        abbr:where([title]) {
            text-decoration: underline dotted;
        }

        /*
Remove the default font size and weight for headings.
*/
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: inherit;
            font-weight: inherit;
        }

        /*
Reset links to optimize for opt-in styling instead of opt-out.
*/
        a {
            color: inherit;
            text-decoration: inherit;
        }

        /*
Add the correct font weight in Edge and Safari.
*/
        b,
        strong {
            font-weight: bolder;
        }

        /*
1. Use the user's configured `mono` font-family by default.
2. Use the user's configured `mono` font-feature-settings by default.
3. Use the user's configured `mono` font-variation-settings by default.
4. Correct the odd `em` font sizing in all browsers.
*/
        code,
        kbd,
        samp,
        pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            /* 1 */
            font-feature-settings: normal;
            /* 2 */
            font-variation-settings: normal;
            /* 3 */
            font-size: 1em;
            /* 4 */
        }

        /*
Add the correct font size in all browsers.
*/
        small {
            font-size: 80%;
        }

        /*
Prevent `sub` and `sup` elements from affecting the line height in all browsers.
*/
        sub,
        sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }

        /*
1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
3. Remove gaps between table borders by default.
*/
        table {
            text-indent: 0;
            /* 1 */
            border-color: inherit;
            /* 2 */
            border-collapse: collapse;
            /* 3 */
        }

        /*
1. Change the font styles in all browsers.
2. Remove the margin in Firefox and Safari.
3. Remove default padding in all browsers.
*/
        button,
        input,
        optgroup,
        select,
        textarea {
            font-family: inherit;
            /* 1 */
            font-feature-settings: inherit;
            /* 1 */
            font-variation-settings: inherit;
            /* 1 */
            font-size: 100%;
            /* 1 */
            font-weight: inherit;
            /* 1 */
            line-height: inherit;
            /* 1 */
            letter-spacing: inherit;
            /* 1 */
            color: inherit;
            /* 1 */
            margin: 0;
            /* 2 */
            padding: 0;
            /* 3 */
        }

        /*
Remove the inheritance of text transform in Edge and Firefox.
*/
        button,
        select {
            text-transform: none;
        }

        /*
1. Correct the inability to style clickable types in iOS and Safari.
2. Remove default button styles.
*/
        button,
        input:where([type='button']),
        input:where([type='reset']),
        input:where([type='submit']) {
            -webkit-appearance: button;
            /* 1 */
            background-color: transparent;
            /* 2 */
            background-image: none;
            /* 2 */
        }

        /*
Use the modern Firefox focus style for all focusable elements.
*/
        :-moz-focusring {
            outline: auto;
        }

        /*
Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
*/
        :-moz-ui-invalid {
            box-shadow: none;
        }

        /*
Add the correct vertical alignment in Chrome and Firefox.
*/
        progress {
            vertical-align: baseline;
        }

        /*
Correct the cursor style of increment and decrement buttons in Safari.
*/
        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
            height: auto;
        }

        /*
1. Correct the odd appearance in Chrome and Safari.
2. Correct the outline style in Safari.
*/
        [type='search'] {
            -webkit-appearance: textfield;
            /* 1 */
            outline-offset: -2px;
            /* 2 */
        }

        /*
Remove the inner padding in Chrome and Safari on macOS.
*/
        ::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        /*
1. Correct the inability to style clickable types in iOS and Safari.
2. Change font properties to `inherit` in Safari.
*/
        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            /* 1 */
            font: inherit;
            /* 2 */
        }

        /*
Add the correct display in Chrome and Safari.
*/
        summary {
            display: list-item;
        }

        /*
Removes the default spacing and border for appropriate elements.
*/
        blockquote,
        dl,
        dd,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        figure,
        p,
        pre {
            margin: 0;
        }

        fieldset {
            margin: 0;
            padding: 0;
        }

        legend {
            padding: 0;
        }

        ol,
        ul,
        menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        /*
Reset default styling for dialogs.
*/
        dialog {
            padding: 0;
        }

        /*
Prevent resizing textareas horizontally by default.
*/
        textarea {
            resize: vertical;
        }

        /*
1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
2. Set the default placeholder color to the user's configured gray 400 color.
*/
        input::placeholder,
        textarea::placeholder {
            opacity: 1;
            /* 1 */
            color: #9ca3af;
            /* 2 */
        }

        /*
Set the default cursor for buttons.
*/
        button,
        [role="button"] {
            cursor: pointer;
        }

        /*
Make sure disabled buttons don't get the pointer cursor.
*/
        :disabled {
            cursor: default;
        }

        /*
1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
   This can trigger a poorly considered lint error in some tools but is included by design.
*/
        img,
        svg,
        video,
        canvas,
        audio,
        iframe,
        embed,
        object {
            display: block;
            /* 1 */
            vertical-align: middle;
            /* 2 */
        }

        /*
Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
*/
        img,
        video {
            max-width: 100%;
            height: auto;
        }

        /* Make elements with the HTML hidden attribute stay hidden by default */
        [hidden] {
            display: none;
        }

        .col-span-1 {
            grid-column: span 1 / span 1;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .ms-auto {
            margin-inline-start: auto;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-4 {
            margin-top: 1rem;
        }

        .mt-6 {
            margin-top: 1.5rem;
        }

        .flex {
            display: flex;
        }

        .table {
            display: table;
        }

        .grid {
            display: grid;
        }

        .h-12 {
            height: 3rem;
        }

        .w-1\/2 {
            width: 50%;
        }

        .w-\[2500px\] {
            width: 2500px;
        }

        .w-fit {
            width: fit-content;
        }

        .w-full {
            width: 100%;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .items-center {
            align-items: center;
        }

        .justify-end {
            justify-content: flex-end;
        }

        .justify-between {
            justify-content: space-between;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .border-b {
            border-bottom-width: 1px;
        }

        .border-b-2 {
            border-bottom-width: 2px;
        }

        .border-t {
            border-top-width: 1px;
        }

        .border-gray-200 {
            --tw-border-opacity: 1;
            border-color: rgb(229 231 235 / var(--tw-border-opacity));
        }

        .border-gray-300 {
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
        }

        .bg-green-600 {
            --tw-bg-opacity: 1;
            background-color: rgb(22 163 74 / var(--tw-bg-opacity));
        }

        .bg-white {
            --tw-bg-opacity: 1;
            background-color: rgb(255 255 255 / var(--tw-bg-opacity));
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-3 {
            padding: 0.75rem;
        }

        .pb-2 {
            padding-bottom: 0.5rem;
        }

        .pt-2 {
            padding-top: 0.5rem;
        }

        .pt-4 {
            padding-top: 1rem;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-sans {
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        .text-2xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .text-lg {
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .text-sm {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }

        .text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        .text-gray-500 {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity));
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity));
        }

        .text-gray-700 {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity));
        }

        .text-gray-800 {
            --tw-text-opacity: 1;
            color: rgb(31 41 55 / var(--tw-text-opacity));
        }

        .text-green-600 {
            --tw-text-opacity: 1;
            color: rgb(22 163 74 / var(--tw-text-opacity));
        }

        .text-white {
            --tw-text-opacity: 1;
            color: rgb(255 255 255 / var(--tw-text-opacity));
        }
    </style>


</head>

<body class="font-sans w-[2500px] p-2">
    <div class="w-full mx-auto ">
        <div class="w-full mx-auto overflow-hidden bg-white rounded-lg">
            <div class="p-3 text-white bg-green-600">
                <h1 class="text-2xl font-bold">{{ __('INVOICE') }}</h1>
            </div>
            <div class="w-full p-3">
                <div class="grid grid-cols-2">
                    <div class="col-span-1 w-fit me-auot">
                        @php
                            $logo = Setting::getSetting(
                                'logo',
                                'https://picsum.photos/536/354',
                            );

                            $name = Setting::getSetting(
                                'name',
                                'Clinic System',
                            );
                            $address = Setting::getSetting(
                                'address',
                                '123 Business Street, Big City, BZ 10001',
                            );
                            $phone = Setting::getSetting(
                                'phone',
                                '123-456-7890',
                            );
                            $email = Setting::getSetting(
                                'email',
                                'billing@yourcompany.com',
                            );
                        @endphp

                        @php
                            $logoPath = storage_path('app/public/' . $logo);
                            $logoData = base64_encode(
                                file_get_contents($logoPath),
                            );
                            $logoSrc = 'data:image/png;base64,' . $logoData;
                        @endphp
                        <img src="{{ $logoSrc }}"
                            alt="{{ $name }}"
                            class="h-12 mb-2" />



                        {{-- <img src="{{ Storage::url($logo) }}"
                            alt="{{ $name }}"
                            class="h-12 mb-2" /> --}}
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ $name }}</h2>
                        <p class="text-sm text-gray-600">{{ $address }}</p>

                        <p class="text-sm text-gray-600">Phone:
                            {{ $phone }}</p>
                        </p>
                        <p class="text-sm text-gray-600">Email:
                            {{ $email }}</p>
                    </div>
                    <div class="col-span-1 text-right w-fit ms-auto">
                        <p class="text-2xl font-bold text-green-600">
                            {{ __('Invoice') }} #
                            {{ $invoice->id }}</p>
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Appointment') }}:
                            @foreach ($appointment_ids as $value)
                                {{ $value . ', ' }}
                            @endforeach

                        </p>
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Printed at') }}:
                            {{ now()->toDateString() }}</p>
                        <p class="text-sm text-gray-600">
                            {{ __('Printed by') }}:
                            {{ $invoice->creator->name }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <h3
                        class="pb-2 text-lg font-semibold text-gray-800 border-b-2 border-gray-300">
                        {{ __('Bill To') }}:</h3>
                    <div class="mt-2">
                        <p class="font-semibold text-gray-800">
                            {{ $patient->full_name }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-sm font-semibold text-white bg-green-600">
                                <th class="p-2 ">
                                    {{ __('No.') }}</th>
                                <th class="p-2 ">{{ __('Description') }}
                                </th>
                                <th class="p-2 ">{{ __('Medical center') }}
                                </th>
                                <th class="p-2 ">{{ __('Doctor') }}</th>
                                <th class="p-2 ">{{ __('Price') }}</th>
                                <th class="p-2 ">{{ __('Discount') }}</th>
                                <th class="p-2 ">{{ __('Patient Fund') }}
                                </th>
                                <th class="p-2 ">
                                    {{ __('Patient Fund Amount') }}</th>
                                <th class="p-2 ">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $index => $appointment)
                                <tr
                                    class="text-sm text-gray-700 border-b border-gray-200">
                                    <td class="p-2 ">
                                        {{ $appointment->id }}</td>
                                    <td class="p-2 ">
                                        {{ $appointment->note }}</td>
                                    <td class="p-2 ">
                                        {{ $appointment->medicalCenter->name }}
                                    </td>
                                    <td class="p-2 ">
                                        {{ $appointment->doctor->name }}</td>
                                    <td class="p-2 ">
                                        {{ $appointment->price }}</td>
                                    <td class="p-2 ">
                                        {{ $appointment->discount }}</td>
                                    <td class="p-2 ">
                                        {{ $appointment->patientFund->name ?? 'N/A' }}
                                    </td>
                                    <td class="p-2 ">
                                        {{ $appointment->patient_fund_total }}
                                    </td>
                                    <td class="p-2 ">
                                        {{ $appointment->total }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="w-1/2 text-right">
                            <div
                                class="flex items-center justify-between pt-2 mt-2 border-t border-gray-300">
                                <div class="text-lg font-bold text-green-600">
                                    {{ __('Total') }}:</div>
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $appointments->sum('total') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-sm text-gray-600">
                    <p class="mt-2 text-center text-gray-500">Thank you!</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
