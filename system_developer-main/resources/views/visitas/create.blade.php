@php
    $buscar = $buscar ?? '';
@endphp
<x-turbo::frame id="modal">
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4">
        <div class="max-h-[90dvh] w-full overflow-y-auto rounded-t-2xl border border-slate-200 bg-white p-4 shadow-xl sm:max-h-[90vh] sm:max-w-2xl sm:rounded-xl sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <h2 class="pr-2 text-lg font-semibold text-slate-800">
                    Nueva visita para <span class="wrap-break-word">{{ $cliente->nombre_completo }}</span>
                </h2>
                <a href="{{ route('clientes.index', $buscar !== '' ? ['buscar' => $buscar] : []) }}" data-turbo-frame="_top" class="shrink-0 self-end text-sm text-slate-500 hover:text-slate-700 sm:self-auto">Cerrar</a>
            </div>

            <form action="{{ route('clientes.visitas.store', $cliente) }}" method="POST" data-turbo-stream="true" class="space-y-4">
                @csrf
                <input type="hidden" name="buscar" value="{{ $buscar }}">
                @include('visitas._form', ['visita' => $visita])

                <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('clientes.index', $buscar !== '' ? ['buscar' => $buscar] : []) }}" data-turbo-frame="_top" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                        Guardar visita
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-turbo::frame>
