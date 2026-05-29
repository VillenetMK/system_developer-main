@php
    $buscarQ = ($buscar ?? '') !== '' ? ['buscar' => $buscar] : [];
@endphp
<tr id="{{ dom_id($cliente) }}" class="hover:bg-slate-50">
    <td class="px-4 py-3">{{ $cliente->dni_ruc }}</td>
    <td class="px-4 py-3">{{ $cliente->nombre_completo }}</td>
    <td class="px-4 py-3">{{ $cliente->nro_celular }}</td>
    <td class="px-4 py-3">{{ $cliente->correo ?: '-' }}</td>
    <td class="px-4 py-3">{{ $cliente->empresa ?: '-' }}</td>
    <td class="px-4 py-3">{{ $cliente->direccion }}</td>
    <td class="px-4 py-3">
        <div class="flex justify-end gap-2">
            <a href="{{ route('clientes.visitas.index', $cliente) }}"
               class="inline-flex items-center justify-center rounded-md border border-sky-300 p-2 text-sky-600 hover:bg-sky-50"
               aria-label="Ver visitas del cliente"
               title="Ver visitas">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </a>
            <a href="{{ route('clientes.visitas.create', array_merge(['cliente' => $cliente], $buscarQ)) }}"
               data-turbo-frame="modal"
               class="inline-flex items-center justify-center rounded-md border border-indigo-300 p-2 text-indigo-600 hover:bg-indigo-50"
               aria-label="Nueva visita"
               title="Nueva visita">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </a>
            <a href="{{ route('clientes.edit', array_merge(['cliente' => $cliente], $buscarQ)) }}"
               data-turbo-frame="modal"
               class="inline-flex items-center justify-center rounded-md border border-slate-300 p-2 text-slate-600 hover:bg-slate-100"
               aria-label="Editar cliente"
               title="Editar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            <a href="{{ route('clientes.delete', array_merge(['cliente' => $cliente], $buscarQ)) }}"
               data-turbo-frame="modal"
               class="inline-flex items-center justify-center rounded-md border border-rose-300 p-2 text-rose-600 hover:bg-rose-50"
               aria-label="Eliminar cliente"
               title="Eliminar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </a>
        </div>
    </td>
</tr>
