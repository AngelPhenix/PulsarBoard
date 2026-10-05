<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#0b0d12">
    <script>
        // Tailwind via CDN (keep it for existing utility classes).
        // Theme-specific styling lives in resources/css/app.css.
        tailwind = {
            config: {
                theme: {
                    extend: {
                        boxShadow: {
                            soft: '0 16px 40px rgba(0,0,0,.45)',
                        },
                    },
                },
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.2.2/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>PulsarBoard</title>
</head>
<body class="min-h-dvh">
    <div id="main-content" class="flex min-h-dvh w-full">

        <x-sidebar :boardList='$boardList ?? null' :board='$board ?? null' />

        <div class="flex-1 px-6 py-8">
            <div class="mx-auto w-full max-w-6xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>