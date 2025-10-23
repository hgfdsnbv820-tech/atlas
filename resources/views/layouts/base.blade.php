<!DOCTYPE html>

@if (\Request::is('rtl'))
    <html lang="ar" dir="rtl">
@else
    <html lang="en">
@endif

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets') }}/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="{{ asset('assets') }}/img/favicon.png" />
    <title>Atlas Roads</title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.5/umd/popper.min.js"></script>

    <!-- Main Styling -->
    <link href="{{ asset('assets') }}/css/styles.css?v=1.0.3" rel="stylesheet" />

    @vite('resources/css/app.css')

    @stack('styles')

    @livewireStyles
</head>

<body class="m-0 font-sans antialiased font-normal text-size-base leading-default bg-gray-50 text-slate-500">
    @hasSection('content')
        @yield('content')
    @else
        {{ $slot ?? '' }}
    @endif

    @livewireScripts
    @vite('resources/js/app.js')
    @stack('scripts')
    <script>
        // Simple toast helper
        function showToast(message) {
            const existing = document.getElementById('live-toast');
            if (existing) existing.remove();
            const div = document.createElement('div');
            div.id = 'live-toast';
            div.className = 'fixed bottom-6 right-6 bg-green-600 text-white px-4 py-2 rounded shadow-lg';
            div.textContent = message;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 3500);
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Livewire browser event
            window.addEventListener('notify', function (e) {
                if (e && e.detail && e.detail.message) {
                    showToast(e.detail.message);
                }
            });

            // Check for session flash rendered into the page
            @if(session('status'))
                showToast(@json(session('status')));
            @endif
        });
    </script>
</body>

<!-- plugin for charts  -->
<script src="{{ asset('assets') }}/js/plugins/chartjs.min.js" async></script>
<!-- plugin for scrollbar  -->
<script src="{{ asset('assets') }}/js/plugins/perfect-scrollbar.min.js" async></script>
<!-- github button -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- main script file  -->
<script src="{{ asset('assets') }}/js/soft-ui-dashboard-tailwind.js?v=1.0.3" async></script>

@yield('scripts')

</html>
