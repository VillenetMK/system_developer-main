<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClienteController extends Controller
{
    public function index(Request $request): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.index', [
            'clientes' => Cliente::query()->porDniRuc($buscar)->latest()->get(),
            'buscar' => $buscar,
        ]);
    }

    public function create(Request $request): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.create', [
            'cliente' => new Cliente(),
            'buscar' => $buscar,
        ]);
    }

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

    public function edit(Request $request, Cliente $cliente): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.edit', compact('cliente', 'buscar'));
    }

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

    public function delete(Request $request, Cliente $cliente): Response
    {
        $buscar = $this->buscarDniRuc($request);

        return response()->view('clientes.delete', compact('cliente', 'buscar'));
    }

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

    private function buscarDniRuc(Request $request): string
    {
        return trim((string) $request->input('buscar', ''));
    }

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
            'dni_ruc.regex' => 'El documento no coincide con el tipo (DNI: 8 digitos, RUC: 11 digitos).',
        ]);

        unset($validated['tipo_doc']);

        return $validated;
    }
}
