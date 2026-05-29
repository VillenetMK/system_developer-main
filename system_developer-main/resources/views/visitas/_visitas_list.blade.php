<div id="visitas_list">
    @if ($visitas->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-12 text-center">
            <p class="text-sm text-slate-500">No hay visitas registradas para este cliente.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="hidden overflow-x-auto lg:block">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3 font-medium">Dia</th>
                            <th class="whitespace-nowrap px-4 py-3 font-medium">Hora estimada</th>
                            <th class="min-w-44 px-4 py-3 font-medium">Direccion</th>
                            <th class="min-w-56 px-4 py-3 font-medium">Detalle</th>
                            <th class="min-w-40 px-4 py-3 font-medium">Observaciones</th>
                            <th class="whitespace-nowrap px-4 py-3 font-medium">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($visitas as $visita)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3 text-slate-800">{{ $visita->dia->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-slate-800">{{ substr((string) $visita->hora_estimada, 0, 5) }}</td>
                                <td class="wrap-break-word px-4 py-3 text-slate-700">{{ $visita->direccion }}</td>
                                <td class="wrap-break-word px-4 py-3 text-slate-600">{{ $visita->detalle ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex max-w-xs items-start gap-2">
                                        <p class="min-w-0 flex-1 wrap-break-word text-slate-600">
                                            {{ $visita->observaciones ? \Illuminate\Support\Str::limit($visita->observaciones, 48) : '—' }}
                                        </p>
                                        <a href="{{ route('clientes.visitas.observaciones.edit', [$cliente, $visita]) }}"
                                           data-turbo-frame="modal"
                                           class="shrink-0 rounded-md p-1 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800"
                                           title="Editar observaciones">
                                            <span class="sr-only">Editar observaciones</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $estadoBadge = [
                                            'en proceso' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
                                            'cancelada' => 'bg-rose-100 text-rose-800 ring-rose-600/20',
                                            'culminada' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
                                        ][$visita->estado] ?? 'bg-slate-100 text-slate-800 ring-slate-600/20';
                                    @endphp
                                    <form action="{{ route('clientes.visitas.estado.update', [$cliente, $visita]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="estado"
                                                onchange="this.form.requestSubmit()"
                                                class="rounded-md px-2.5 py-1.5 text-xs font-medium ring-1 ring-inset focus:outline-none {{ $estadoBadge }}">
                                            <option value="en proceso" @selected($visita->estado === 'en proceso')>En proceso</option>
                                            <option value="cancelada" @selected($visita->estado === 'cancelada')>Cancelada</option>
                                            <option value="culminada" @selected($visita->estado === 'culminada')>Culminada</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-3 border-t border-slate-100 p-4 lg:hidden">
                @foreach ($visitas as $visita)
                    <div class="rounded-lg border border-slate-200 bg-slate-50/80 p-4">
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-sm font-medium text-slate-900">{{ $visita->dia->format('d/m/Y') }}</span>
                            @php
                                $estadoBadge = [
                                    'en proceso' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
                                    'cancelada' => 'bg-rose-100 text-rose-800 ring-rose-600/20',
                                    'culminada' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
                                ][$visita->estado] ?? 'bg-slate-100 text-slate-800 ring-slate-600/20';
                            @endphp
                            <form action="{{ route('clientes.visitas.estado.update', [$cliente, $visita]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="estado"
                                        onchange="this.form.requestSubmit()"
                                        class="rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset focus:outline-none {{ $estadoBadge }}">
                                    <option value="en proceso" @selected($visita->estado === 'en proceso')>En proceso</option>
                                    <option value="cancelada" @selected($visita->estado === 'cancelada')>Cancelada</option>
                                    <option value="culminada" @selected($visita->estado === 'culminada')>Culminada</option>
                                </select>
                            </form>
                        </div>
                        <p class="text-xs text-slate-500">
                            Hora estimada · <span class="font-medium text-slate-800">{{ substr((string) $visita->hora_estimada, 0, 5) }}</span>
                        </p>
                        <p class="mt-2 text-xs text-slate-500">Direccion</p>
                        <p class="wrap-break-word text-sm text-slate-800">{{ $visita->direccion }}</p>
                        @if ($visita->detalle)
                            <p class="mt-2 text-xs text-slate-500">Detalle</p>
                            <p class="wrap-break-word text-sm text-slate-700">{{ $visita->detalle }}</p>
                        @else
                            <p class="mt-2 text-xs text-slate-400">Sin detalle</p>
                        @endif
                        <div class="mt-3 flex items-start justify-between gap-2 rounded-lg border border-slate-200 bg-white p-3">
                            <div class="min-w-0 flex-1">
                                <p class="mb-1 text-xs font-medium text-slate-500">Observaciones</p>
                                <p class="wrap-break-word text-sm text-slate-700">{{ $visita->observaciones ?: 'Sin observaciones' }}</p>
                            </div>
                            <a href="{{ route('clientes.visitas.observaciones.edit', [$cliente, $visita]) }}"
                               data-turbo-frame="modal"
                               class="shrink-0 rounded-md p-2 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800"
                               title="Editar observaciones">
                                <span class="sr-only">Editar observaciones</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
