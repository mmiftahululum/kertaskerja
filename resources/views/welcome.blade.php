<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lembar Kerja - Manajemen Tugas Anda</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100">
    <div class="relative min-h-screen flex flex-col items-center justify-center">
        @if (Route::has('login'))
            <div class="absolute top-0 right-0 p-6 text-right">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Masuk</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Daftar</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-4xl mx-auto text-center px-6">
            
            <div class="flex justify-center mb-4">
                <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">
                Selamat Datang di <span class="text-indigo-600">Lembar Kerja</span>
            </h1>

            <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600">
                Atur, lacak, dan selesaikan semua tugas Anda di satu tempat. Ubah kekacauan menjadi kejelasan dan tingkatkan produktivitas tim Anda hari ini.
            </p>

            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-block px-8 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150">
                    Mulai Bekerja
                </a>
                <a href="#" class="inline-block px-8 py-3 bg-white text-gray-700 font-semibold rounded-lg shadow-md hover:bg-gray-50 transition duration-150">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>

      {{-- Kode BARU --}}
<footer class=" bottom-0 w-full p-4 mt-10">
    <div class="text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Lembar Kerja. All rights reserved.
    </div>
</footer>
    </div>
</body>
</html>