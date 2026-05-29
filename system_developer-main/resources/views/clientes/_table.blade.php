<div id="clientes_table">
    <div class="space-y-3 lg:hidden">
        @forelse ($clientes as $cliente)
            @include('clientes._cliente_card', ['cliente' => $cliente, 'buscar' => $buscar ?? ''])
        @empty
            <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                {{ ($buscar ?? '') !== '' ? 'No se encontraron clientes con ese DNI/RUC.' : 'Aun no hay clientes registrados.' }}
            </p>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto lg:block">
        <table class="min-w-[900px] w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="whitespace-nowrap px-3 py-3 font-medium sm:px-4">DNI/RUC</th>
                    <th class="whitespace-nowrap px-3 py-3 font-medium sm:px-4">Nombre completo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-medium sm:px-4">Celular</th>
                    <th class="whitespace-nowrap px-3 py-3 font-medium sm:px-4">Correo</th>
                    <th class="whitespace-nowrap px-3 py-3 font-medium sm:px-4">Empresa</th>
                    <th class="min-w-40 px-3 py-3 font-medium sm:px-4">Direccion</th>
                    <th class="whitespace-nowrap px-3 py-3 text-right font-medium sm:px-4">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse ($clientes as $cliente)
                    @include('clientes._cliente_row', ['cliente' => $cliente, 'buscar' => $buscar ?? ''])
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                            {{ ($buscar ?? '') !== '' ? 'No se encontraron clientes con ese DNI/RUC.' : 'Aun no hay clientes registrados.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
