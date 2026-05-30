<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class VisitaController extends Controller
{
    /**
     * Muestra todas las visitas registradas para un cliente.
     */
    public function index(Cliente $cliente): Response
    {
        $visitas = $this->visitasOrdenadas($cliente);

        return response()->view('visitas.index', compact('cliente', 'visitas'));
    }

    /**
     * Muestra el formulario para crear una visita del cliente.
     *
     * La dirección se precarga con la dirección registrada en el cliente.
     */
    public function create(Request $request, Cliente $cliente): Response
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $visita = new Visita([
            'direccion' => $cliente->direccion,
            'estado' => 'en proceso',
        ]);

        return response()->view('visitas.create', compact('cliente', 'visita', 'buscar'));
    }

    /**
     * Muestra el detalle de una visita dentro de un modal.
     */
    public function showModal(Cliente $cliente, Visita $visita): Response
    {
        $this->validarPertenencia($cliente, $visita);

        return response()->view('visitas.show_modal', compact('cliente', 'visita'));
    }

    /**
     * Muestra el formulario modal para editar observaciones de una visita.
     */
    public function editObservaciones(Cliente $cliente, Visita $visita): Response
    {
        $this->validarPertenencia($cliente, $visita);

        return response()->view('visitas.observaciones_modal', compact('cliente', 'visita'));
    }

    /**
     * Actualiza las observaciones de una visita.
     *
     * Si el campo llega vacío, se guarda como null para mantener limpia la base de datos.
     */
    public function updateObservaciones(Request $request, Cliente $cliente, Visita $visita)
    {
        $this->validarPertenencia($cliente, $visita);

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

    /**
     * Registra una nueva visita para el cliente indicado.
     */
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

    /**
     * Cambia el estado de una visita.
     *
     * Estados permitidos: en proceso, cancelada y culminada.
     */
    public function updateEstado(Request $request, Cliente $cliente, Visita $visita)
    {
        $this->validarPertenencia($cliente, $visita);

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

    /**
     * Valida los datos necesarios para crear una visita.
     */
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
     * Devuelve las visitas del cliente ordenadas desde la más reciente.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Visita>
     */
    private function visitasOrdenadas(Cliente $cliente)
    {
        return $cliente->visitas()
            ->orderByDesc('dia')
            ->orderByDesc('hora_estimada')
            ->get();
    }

    /**
     * Evita acceder a una visita que no pertenece al cliente actual.
     */
    private function validarPertenencia(Cliente $cliente, Visita $visita): void
    {
        if ((int) $visita->cliente_id !== (int) $cliente->id) {
            abort(404);
        }
    }
}
