<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClienteController extends Controller
{
    /**
     * Muestra el listado de clientes.
     *
     * También permite filtrar por DNI o RUC usando el parámetro "buscar".
     */
    public function index(Request $request): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.index', [
            'clientes' => Cliente::query()->porDniRuc($buscar)->latest()->get(),
            'buscar' => $buscar,
        ]);
    }

    /**
     * Muestra el formulario para registrar un nuevo cliente.
     */
    public function create(Request $request): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.create', [
            'cliente' => new Cliente(),
            'buscar' => $buscar,
        ]);
    }

    /**
     * Guarda un nuevo cliente en la base de datos.
     *
     * Si la petición viene desde Turbo, actualiza la tabla y cierra el modal
     * sin recargar toda la página.
     */
    public function store(Request $request)
    {
        $buscar = $this->buscarDniRuc($request);
        $cliente = Cliente::query()->create($this->validatedData($request));

        if ($request->wantsTurboStream()) {
            return turbo_stream([
                turbo_stream()->replace('clientes_table', view('clientes._table', [
                    'clientes' => Cliente::query()->porDniRuc($buscar)->latest()->get(),
                    'buscar' => $buscar,
                ])),
                turbo_stream()->update('modal', ''),
            ]);
        }

        return redirect()
            ->route('clientes.index', $buscar !== '' ? ['buscar' => $buscar] : [])
            ->with('status', "Cliente {$cliente->nombre_completo} creado correctamente.");
    }

    /**
     * Muestra el formulario de edición de un cliente existente.
     */
    public function edit(Request $request, Cliente $cliente): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.edit', compact('cliente', 'buscar'));
    }

    /**
     * Actualiza la información de un cliente.
     *
     * Soporta respuesta normal por redirección y respuesta Turbo para modales.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $buscar = $this->buscarDniRuc($request);
        $cliente->update($this->validatedData($request));

        if ($request->wantsTurboStream()) {
            return turbo_stream([
                turbo_stream()->replace('clientes_table', view('clientes._table', [
                    'clientes' => Cliente::query()->porDniRuc($buscar)->latest()->get(),
                    'buscar' => $buscar,
                ])),
                turbo_stream()->update('modal', ''),
            ]);
        }

        return redirect()
            ->route('clientes.index', $buscar !== '' ? ['buscar' => $buscar] : [])
            ->with('status', "Cliente {$cliente->nombre_completo} actualizado correctamente.");
    }

    /**
     * Muestra una vista de confirmación antes de eliminar el cliente.
     */
    public function delete(Request $request, Cliente $cliente): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.delete', compact('cliente', 'buscar'));
    }

    /**
     * Elimina un cliente de la base de datos.
     *
     * Cuando se usa Turbo, refresca la tabla de clientes y limpia el modal.
     */
    public function destroy(Request $request, Cliente $cliente)
    {
        $buscar = $this->buscarDniRuc($request);
        $cliente->delete();

        if ($request->wantsTurboStream()) {
            return turbo_stream([
                turbo_stream()->replace('clientes_table', view('clientes._table', [
                    'clientes' => Cliente::query()->porDniRuc($buscar)->latest()->get(),
                    'buscar' => $buscar,
                ])),
                turbo_stream()->update('modal', ''),
            ]);
        }

        return redirect()
            ->route('clientes.index', $buscar !== '' ? ['buscar' => $buscar] : [])
            ->with('status', 'Cliente eliminado correctamente.');
    }

    /**
     * Obtiene y normaliza el término de búsqueda por DNI/RUC.
     */
    private function buscarDniRuc(Request $request): string
    {
        return trim((string) $request->input('buscar', ''));
    }

    /**
     * Valida los datos enviados desde los formularios de cliente.
     *
     * La longitud del documento cambia según el tipo seleccionado:
     * DNI = 8 dígitos, RUC = 11 dígitos.
     */
    private function validatedData(Request $request): array
    {
        $tipo = $request->input('tipo_doc', 'dni');
        if ($tipo !== 'ruc') {
            $tipo = 'dni';
        }

        $validated = $request->validate([
            'tipo_doc' => ['nullable', 'string', 'in:dni,ruc'],
            'dni_ruc' => array_merge(
                ['required', 'string'],
                $tipo === 'ruc'
                    ? ['regex:/^\d{11}$/']
                    : ['regex:/^\d{8}$/']
            ),
            'nombre_completo' => ['required', 'string', 'max:150'],
            'nro_celular' => ['required', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'direccion' => ['required', 'string', 'max:255'],
        ], [
            'dni_ruc.regex' => 'El documento no coincide con el tipo (DNI: 8 dígitos, RUC: 11 dígitos).',
        ]);

        // tipo_doc solo sirve para validar; no se guarda en la tabla clientes.
        unset($validated['tipo_doc']);

        return $validated;
    }
}
