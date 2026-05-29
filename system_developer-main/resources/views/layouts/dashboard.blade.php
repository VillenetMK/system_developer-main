<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }} | Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-800 antialiased">
        <input type="checkbox" id="drawer-nav" class="peer sr-only">

        <label for="drawer-nav" class="fixed inset-0 z-40 bg-slate-900/0 opacity-0 pointer-events-none transition-opacity duration-200 peer-checked:pointer-events-auto peer-checked:bg-slate-900/40 peer-checked:opacity-100 lg:hidden" aria-hidden="true"></label>

        <aside class="fixed inset-y-0 left-0 z-50 flex h-full w-72 max-w-[88vw] -translate-x-full flex-col border-r border-slate-200 bg-white shadow-xl transition-transform duration-300 ease-out peer-checked:translate-x-0 lg:hidden">
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-200 px-4">
                <span class="text-sm font-semibold text-slate-800">Menu</span>
                <label for="drawer-nav" class="cursor-pointer rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="sr-only">Cerrar menu</span>
                </label>
            </div>
            <div class="overflow-y-auto p-4">
                <p class="mb-4 text-xs uppercase tracking-wide text-slate-400">Secciones</p>
                <nav class="space-y-2 text-sm">
                    <a href="{{ route('clientes.index') }}" data-turbo-frame="_top" class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('clientes.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100' }}">
                        Clientes
                    </a>
                    <a href="{{ route('calendario.index') }}" data-turbo-frame="_top" class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('calendario.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100' }}">
                        Calendario
                    </a>
                </nav>
            </div>
        </aside>

        <div class="min-h-screen">
            <nav class="flex min-h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:px-6">
                <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
                    <label for="drawer-nav" class="cursor-pointer rounded-lg border border-slate-200 bg-white p-2 text-slate-600 hover:bg-slate-50 lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="sr-only">Abrir menu</span>
                    </label>
                    <div class="h-9 w-9 shrink-0 rounded-lg bg-indigo-600 flex items-center justify-center font-semibold text-white">
                        OD
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-xs text-slate-500 leading-none sm:text-sm">Panel</p>
                        <p class="truncate font-semibold leading-none mt-0.5 sm:mt-1">Odin Developer</p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <p class="hidden text-sm text-slate-500 sm:block">{{ auth()->user()?->name }}</p>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100 sm:text-sm">
                            Cerrar sesion
                        </button>
                    </form>
                </div>
            </nav>

            <div class="flex flex-col lg:flex-row lg:min-h-[calc(100vh-4rem)]">
                <aside class="hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white p-4 lg:flex">
                    <p class="mb-4 text-xs uppercase tracking-wide text-slate-400">Menu</p>
                    <nav class="space-y-2 text-sm">
                        <a href="{{ route('clientes.index') }}" class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('clientes.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100' }}">
                            Clientes
                        </a>
                        <a href="{{ route('calendario.index') }}" class="flex items-center rounded-lg px-3 py-2 font-medium {{ request()->routeIs('calendario.*') ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100' }}">
                            Calendario
                        </a>
                    </nav>
                </aside>

                <main class="min-w-0 flex-1 p-4 sm:p-6">
                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        <x-turbo::frame id="modal"></x-turbo::frame>
    </body>
</html>
