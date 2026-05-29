<x-turbo::frame id="modal">
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4">
        <div class="max-h-[90dvh] w-full overflow-y-auto rounded-t-2xl border border-slate-200 bg-white p-4 shadow-xl sm:max-h-[90vh] sm:max-w-lg sm:rounded-xl sm:p-6">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-slate-900">Observaciones de la visita</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $visita->dia->format('d/m/Y') }}
                        · {{ substr((string) $visita->hora_estimada, 0, 5) }}
                    </p>
                </div>
                <a href="{{ route('turbo.modal-empty') }}" data-turbo-frame="modal" class="shrink-0 rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                    <span class="sr-only">Cerrar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            <form action="{{ route('clientes.visitas.observaciones.update', [$cliente, $visita]) }}" method="POST" data-turbo-stream="true" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="observaciones" class="mb-1.5 block text-sm font-medium text-slate-700">Notas u observaciones</label>
                    <textarea id="observaciones"
                              name="observaciones"
                              rows="6"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                              placeholder="Ej. Cliente no estaba, dejar folleto, coordinar retorno...">{{ old('observaciones', $visita->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                    <a href="{{ route('turbo.modal-empty') }}" data-turbo-frame="modal" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                        Guardar observaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-turbo::frame>
