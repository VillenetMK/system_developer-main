@extends('layouts.dashboard')

@section('content')
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Calendario de visitas</h1>
            <p class="text-sm text-slate-500">Vista mensual de todas las visitas programadas.</p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <form action="{{ route('calendario.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="month" value="{{ $monthDate->format('Y-m') }}">
                <label for="estado" class="text-sm font-medium text-slate-600">Estado</label>
                <select id="estado" name="estado" onchange="this.form.requestSubmit()" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-indigo-500 focus:outline-none">
                    <option value="todos" @selected($selectedEstado === 'todos')>Todos</option>
                    <option value="en proceso" @selected($selectedEstado === 'en proceso')>En proceso</option>
                    <option value="cancelada" @selected($selectedEstado === 'cancelada')>Cancelada</option>
                    <option value="culminada" @selected($selectedEstado === 'culminada')>Culminada</option>
                </select>
            </form>

            <a href="{{ route('calendario.index', ['month' => $prevMonth, 'estado' => $selectedEstado]) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Mes anterior
            </a>
            <span class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-800">
                {{ $monthDate->translatedFormat('F Y') }}
            </span>
            <a href="{{ route('calendario.index', ['month' => $nextMonth, 'estado' => $selectedEstado]) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                Mes siguiente
                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
            @foreach ($weekDays as $dayName)
                <div class="px-2 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 sm:px-3">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        @foreach ($weeks as $week)
            <div class="grid grid-cols-7 border-b border-slate-100 last:border-b-0">
                @foreach ($week as $day)
                    @php
                        $isCurrentMonth = $day['is_current_month'];
                        $isToday = $day['is_today'];
                        $visitas = $day['visitas'];
                    @endphp
                    <div class="min-h-28 border-r border-slate-100 p-2 last:border-r-0 sm:min-h-36 sm:p-3 {{ $isCurrentMonth ? 'bg-white' : 'bg-slate-50/70' }}">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full px-1 text-xs font-semibold {{ $isToday ? 'bg-indigo-600 text-white' : 'text-slate-700' }}">
                                {{ $day['date']->day }}
                            </span>
                            @if ($visitas->isNotEmpty())
                                <span class="text-[10px] font-medium text-slate-400 sm:text-xs">{{ $visitas->count() }} visita(s)</span>
                            @endif
                        </div>

                        <div class="space-y-1.5">
                            @foreach ($visitas->take(3) as $visita)
                                @php
                                    $estadoCard = [
                                        'en proceso' => 'border-amber-200 bg-amber-50 text-amber-900 hover:bg-amber-100',
                                        'cancelada' => 'border-rose-200 bg-rose-50 text-rose-900 hover:bg-rose-100',
                                        'culminada' => 'border-emerald-200 bg-emerald-50 text-emerald-900 hover:bg-emerald-100',
                                    ][$visita->estado] ?? 'border-slate-200 bg-slate-50 text-slate-800 hover:bg-slate-100';
                                @endphp
                                <a href="{{ route('clientes.visitas.show', [$visita->cliente, $visita]) }}"
                                   data-turbo-frame="modal"
                                   class="block rounded-md border px-2 py-1 text-[11px] sm:text-xs {{ $estadoCard }}">
                                    <p class="font-semibold">{{ substr((string) $visita->hora_estimada, 0, 5) }}</p>
                                    <p class="truncate">{{ $visita->cliente->nombre_completo }}</p>
                                </a>
                            @endforeach

                            @if ($visitas->count() > 3)
                                <p class="text-[11px] text-slate-500 sm:text-xs">+ {{ $visitas->count() - 3 }} mas</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
