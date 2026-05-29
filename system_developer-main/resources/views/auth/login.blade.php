<x-guest-layout>
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="hidden bg-indigo-600 px-12 py-14 text-white lg:flex lg:flex-col lg:justify-between xl:px-20">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-white/20 font-semibold text-white">
                    OD
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-indigo-100">Panel</p>
                    <p class="text-sm font-semibold text-white">Odin Developer</p>
                </div>
            </div>

            <div class="max-w-xl">
                <p class="text-base uppercase tracking-[0.2em] text-indigo-100">Acceso seguro</p>
                <h1 class="mt-4 text-6xl font-semibold leading-tight xl:text-7xl">Gestiona clientes y visitas en un solo panel</h1>
                <p class="mt-7 text-xl text-indigo-100 xl:text-2xl">
                    Inicia sesion para continuar con el seguimiento comercial de Odin Developer.
                </p>
            </div>

            <p class="text-xs text-indigo-100/90">Sistema interno</p>
        </div>

        <div class="flex items-center justify-center bg-slate-100 px-4 py-10 sm:px-6 lg:px-12">
            <div class="w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-10 shadow-sm sm:p-12">
                <div class="mb-6 lg:hidden">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 font-semibold text-white">
                        OD
                    </div>
                    <p class="text-xs uppercase tracking-wide text-slate-500">Panel</p>
                    <p class="text-sm font-semibold text-slate-900">Odin Developer</p>
                </div>

                <h2 class="text-4xl font-semibold text-slate-900">Iniciar sesion</h2>
                <p class="mt-3 text-lg text-slate-500">Ingresa con tu cuenta para acceder al sistema.</p>

                <x-auth-session-status class="mt-5 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4 text-lg text-emerald-700" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-lg font-medium text-slate-700">Correo</label>
                        <input id="email"
                               class="block w-full rounded-lg border border-slate-300 px-5 py-4 text-lg text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-lg text-rose-600" />
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-lg font-medium text-slate-700">Contrasena</label>
                        <input id="password"
                               class="block w-full rounded-lg border border-slate-300 px-5 py-4 text-lg text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-lg text-rose-600" />
                    </div>

                    <div class="flex items-center justify-between gap-2">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-lg text-slate-600">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
                            Recordarme
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-lg text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                                Olvide mi contrasena
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-4 text-lg font-medium text-white hover:bg-indigo-700">
                        Entrar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
