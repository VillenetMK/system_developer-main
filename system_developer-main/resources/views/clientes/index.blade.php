@extends('layouts.dashboard')

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
        <div class="mb-5 space-y-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-lg font-semibold sm:text-xl">Clientes</h1>
                <a href="{{ route('clientes.create', $buscar !== '' ? ['buscar' => $buscar] : []) }}"
                   data-turbo-frame="modal"
                   class="inline-flex w-full shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 sm:w-auto sm:self-auto">
                    Nuevo cliente
                </a>
            </div>

            <form method="GET" action="{{ route('clientes.index') }}" class="flex w-full flex-col gap-2 sm:flex-row sm:items-center">
                <div class="min-w-0 flex-1">
                    <label for="buscar" class="sr-only">Buscar por DNI/RUC</label>
                    <input type="search" id="buscar" name="buscar" value="{{ $buscar }}" maxlength="20" placeholder="Buscar por DNI/RUC" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                </div>
                <div class="flex w-full shrink-0 gap-2 sm:w-auto">
                    <button type="submit" aria-label="Buscar" title="Buscar" class="inline-flex h-[38px] w-[38px] shrink-0 items-center justify-center rounded-lg bg-slate-800 text-white hover:bg-slate-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 18a7 7 0 110-14 7 7 0 010 14z" />
                        </svg>
                        <span class="sr-only">Buscar</span>
                    </button>
                    @if ($buscar !== '')
                        <a href="{{ route('clientes.index') }}" class="inline-flex min-h-[38px] flex-1 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:flex-initial">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @include('clientes._table', ['clientes' => $clientes, 'buscar' => $buscar])
    </div>
@endsection
