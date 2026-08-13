<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">

        {{-- Top Navbar / Header Utama --}}
        <header class="bg-white border-b border-slate-100 sticky top-0 z-30 shadow-2xs">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
                
                {{-- App Brand / Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-xs font-bold text-sm">
                        C
                    </div>
                    <span class="font-bold text-slate-800 text-sm tracking-tight hidden sm:inline-block">Cashflow Tracker</span>
                </a>

                {{-- User Info & Logout Form --}}
                <div class="flex items-center gap-3">
                    @auth
                        <span class="text-xs font-medium text-slate-500 hidden sm:inline-block">
                            {{ auth()->user()->name }}
                        </span>
                        <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit" 
                                class="px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition-all active:scale-95 cursor-pointer"
                            >
                                Keluar
                            </button>
                        </form>
                    @endauth
                </div>

            </div>
        </header>

        {{-- Main Content Slot --}}
        {{ $slot }}

        @livewireScripts
    </body>
</html>