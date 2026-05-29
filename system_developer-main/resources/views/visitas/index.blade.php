@extends('layouts.dashboard')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('clientes.index') }}" class="mb-2 inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Volver a clientes
            </a>
            <h1 class="text-lg font-semibold text-slate-900 sm:text-xl">Visitas registradas</h1>
            <p class="mt-1 truncate text-sm text-slate-500">
                Cliente: <span class="font-medium text-slate-800">{{ $cliente->nombre_completo }}</span>
                · DNI/RUC: {{ $cliente->dni_ruc }}
            </p>
        </div>
        <a href="{{ route('clientes.visitas.create', $cliente) }}"
           data-turbo-frame="modal"
           class="inline-flex w-full shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 sm:w-auto">
            Nueva visita
        </a>
    </div>

    @include('visitas._visitas_list', compact('cliente', 'visitas'))
@endsection
