@php
    use App\Models\Setting;
    $logo = Setting::getSetting('logo', 'https://picsum.photos/536/354');
    $name = Setting::getSetting('name', 'Clinic System');

@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect"
        href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap"
        rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header
                    class="flex flex-col items-center justify-center gap-6 ">
                    <div class="flex lg:justify-center ">
                        <img src="{{ Storage::url($logo) }}"
                            class="hidden h-8 lg:block"
                            alt="{{ $name }}">
                    </div>
                    @if (Route::has('login'))
                        <livewire:welcome.navigation />
                    @endif
                </header>

                <main class="mt-6">

                </main>

                <footer
                    class="py-16 text-sm text-center text-black dark:text-white/70">
                    {{-- {{ __('Made With Love') }} --}}
                </footer>
            </div>
        </div>
    </div>
</body>

</html>
