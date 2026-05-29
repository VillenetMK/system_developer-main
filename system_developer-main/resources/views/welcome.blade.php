<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} | Dashboard</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-100 text-slate-800 antialiased">
        <div class="min-h-screen">
            <nav class="h-16 border-b border-slate-200 bg-white px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-semibold">
                        OD
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 leading-none">Panel</p>
                        <p class="font-semibold leading-none mt-1">Odin Developer</p>
                    </div>
                </div>
                <div class="text-sm text-slate-500">
                    Navbar lista
                </div>
            </nav>

            <div class="flex">
                <aside class="w-64 min-h-[calc(100vh-4rem)] border-r border-slate-200 bg-white p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400 mb-4">Menu</p>

                    <nav class="space-y-2 text-sm">
                        <a href="#" class="flex items-center rounded-lg px-3 py-2 bg-slate-100 text-slate-900 font-medium">
                            Dashboard
                        </a>
                        <a href="#" class="flex items-center rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100">
                            Reportes
                        </a>
                        <a href="#" class="flex items-center rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100">
                            Usuarios
                        </a>
                        <a href="#" class="flex items-center rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100">
                            Configuracion
                        </a>
                </nav>
                </aside>

                <main class="flex-1 p-6">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8">
                        <h1 class="text-xl font-semibold mb-2">Dashboard base creado</h1>
                        <p class="text-slate-500">
                            Este espacio queda preparado para que me digas el contenido que quieres agregar.
                        </p>
                </div>
            </main>
            </div>
        </div>
    </body>
</html>
