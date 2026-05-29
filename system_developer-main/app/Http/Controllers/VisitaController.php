<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class VisitaController extends Controller
{
    public function index(Cliente $cliente): Response
    {
        $visitas = $this->visitasOrdenadas($cliente);

        return response()->view('visitas.index', compact('cliente', 'visitas'));
    }

    public function create(Request $request, Cliente $cliente): Response
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $visita = new Visita([
            'direccion' => $cliente->direccion,
            'estado' => 'en proceso',
        ]);

        return response()->view('visitas.create', compact('cliente', 'visita', 'buscar'));
    }

    public function showModal(Cliente $cliente, Visita $visita): Response
    {
        if ((int) $visita->cliente_id !== (int) $cliente->id) {
            abort(404);
        }

        return response()->view('visitas.show_modal', compact('cliente', 'visita'));
    }

    public function editObservaciones(Cliente $cliente, Visita $visita): Response
    {
        if ((int) $visita->cliente_id !== (int) $cliente->id) {
            abort(404);
        }

        return response()->view('visitas.observaciones_modal', compact('cliente', 'visita'));
    }

    public function updateObservaciones(Request $request, Cliente $cliente, Visita $visita)
    {
        if ((int) $visita->cliente_id !== (int) $cliente->id) {
            abort(404);
        }

        $validated = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:10000'],
        ]);

        $visita->update([
            'observaciones' => $validated['observaciones'] !== null && $validated['observaciones'] !== ''
                ? $validated['observaciones']
                : null,
        ]);

        if ($request->wantsTurboStream()) {
            $visitas = $this->visitasOrdenadas($cliente);

            return turbo_stream([
                turbo_stream()->replace('visitas_list', view('visitas._visitas_list', compact('cliente', 'visitas'))),
                turbo_stream()->update('modal', ''),
            ]);
        }

        return redirect()
            ->route('clientes.visitas.index', $cliente)
            ->with('status', 'Observaciones guardadas correctamente.');
    }

    public function store(Request $request, Cliente $cliente)
    {
        $buscar = trim((string) $request->input('buscar', ''));
        $cliente->visitas()->create($this->validatedData($request));

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
            ->with('status', "Visita registrada para {$cliente->nombre_completo}.");
    }

    public function updateEstado(Request $request, Cliente $cliente, Visita $visita)
    {
        if ((int) $visita->cliente_id !== (int) $cliente->id) {
            abort(404);
        }

        $validated = $request->validate([
            'estado' => ['required', 'string', Rule::in(['en proceso', 'cancelada', 'culminada'])],
        ]);

        $visita->update([
            'estado' => $validated['estado'],
        ]);

        return redirect()
            ->route('clientes.visitas.index', $cliente)
            ->with('status', 'Estado de visita actualizado correctamente.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'dia' => ['required', 'date'],
            'hora_estimada' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'direccion' => ['required', 'string', 'max:255'],
            'detalle' => ['nullable', 'string'],
            'estado' => ['required', 'string', Rule::in(['en proceso', 'cancelada', 'culminada'])],
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Visita>
     */
    private function visitasOrdenadas(Cliente $cliente)
    {
        return $cliente->visitas()
            ->orderByDesc('dia')
            ->orderByDesc('hora_estimada')
            ->get();
    }
}
