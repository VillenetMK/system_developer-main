<x-turbo::frame id="modal">
    <div class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4">
        <div class="max-h-[90dvh] w-full overflow-y-auto rounded-t-2xl border border-slate-200 bg-white p-4 shadow-xl sm:max-h-[90vh] sm:max-w-xl sm:rounded-xl sm:p-6">
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Detalle de visita</h2>
                    <p class="text-sm text-slate-500">{{ $cliente->nombre_completo }} · {{ $cliente->dni_ruc }}</p>
                </div>
                <a href="{{ url()->previous() }}" data-turbo-frame="_top" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                    <span class="sr-only">Cerrar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>

            <div class="space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Fecha</span>
                    <span class="font-medium text-slate-900">{{ $visita->dia->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Hora estimada</span>
                    <span class="font-medium text-slate-900">{{ substr((string) $visita->hora_estimada, 0, 5) }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Estado</span>
                    @php
                        $estadoBadge = [
                            'en proceso' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
                            'cancelada' => 'bg-rose-100 text-rose-800 ring-rose-600/20',
                            'culminada' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
                        ][$visita->estado] ?? 'bg-slate-100 text-slate-800 ring-slate-600/20';
                    @endphp
                    <span class="inline-flex rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $estadoBadge }}">
                        {{ ucfirst($visita->estado) }}
                    </span>
                </div>
                <div>
                    <p class="mb-1 text-slate-500">Direccion</p>
                    <p class="wrap-break-word text-slate-900">{{ $visita->direccion }}</p>
                </div>
                <div>
                    <p class="mb-1 text-slate-500">Detalle</p>
                    <p class="wrap-break-word text-slate-900">{{ $visita->detalle ?: 'Sin detalle' }}</p>
                </div>
                <div>
                    <p class="mb-1 text-slate-500">Observaciones</p>
                    <p class="wrap-break-word whitespace-pre-wrap text-slate-900">{{ $visita->observaciones ?: 'Sin observaciones' }}</p>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <a href="{{ route('clientes.visitas.index', $cliente) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
                    Ir a visitas del cliente
                </a>
            </div>
        </div>
    </div>
</x-turbo::frame>
