@php
    $buscar = $buscar ?? '';
@endphp
<x-turbo::frame id="modal">
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4">
        <div class="max-h-[90dvh] w-full overflow-y-auto rounded-t-2xl border border-slate-200 bg-white p-4 shadow-xl sm:max-h-[90vh] sm:max-w-md sm:rounded-xl sm:p-6">
            <h2 class="text-lg font-semibold text-slate-800">Eliminar cliente</h2>
            <p class="mt-2 text-sm text-slate-600">
                Vas a eliminar a <span class="font-medium wrap-break-word">{{ $cliente->nombre_completo }}</span>. Esta accion no se puede deshacer.
            </p>

            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" data-turbo-stream="true" class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                @csrf
                @method('DELETE')
                <input type="hidden" name="buscar" value="{{ $buscar }}">
                <a href="{{ route('clientes.index', $buscar !== '' ? ['buscar' => $buscar] : []) }}" data-turbo-frame="_top" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-100">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-rose-700">
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</x-turbo::frame>
