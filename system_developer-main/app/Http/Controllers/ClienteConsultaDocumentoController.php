<?php

namespace App\Http\Controllers;

use App\Services\ApiPeruDevService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ClienteConsultaDocumentoController extends Controller
{
    /**
     * Consulta datos de DNI o RUC usando ApiPeru.
     *
     * Esta acción se usa desde el formulario de clientes para autocompletar
     * campos como nombre completo, empresa y dirección.
     */
    public function __invoke(Request $request, ApiPeruDevService $apiPeruDev): JsonResponse
    {
        $validated = $request->validate([
            'tipo_doc' => ['required', 'string', Rule::in(['dni', 'ruc'])],
            'documento' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        $numero = $validated['documento'];
        $tipo = $validated['tipo_doc'];

        // Validación exacta según el tipo de documento peruano.
        if ($tipo === 'dni' && strlen($numero) !== 8) {
            return response()->json(['message' => 'El DNI debe tener 8 dígitos.'], 422);
        }

        if ($tipo === 'ruc' && strlen($numero) !== 11) {
            return response()->json(['message' => 'El RUC debe tener 11 dígitos.'], 422);
        }

        // Selecciona el endpoint correspondiente del servicio ApiPeru.
        $resultado = $tipo === 'ruc'
            ? $apiPeruDev->consultarRuc($numero)
            : $apiPeruDev->consultarDni($numero);

        if (! ($resultado['success'] ?? false)) {
            return response()->json([
                'message' => $resultado['message'] ?? 'Respuesta inválida desde ApiPeru.',
            ], 422);
        }

        $data = $resultado['data'] ?? null;
        if (! is_array($data)) {
            return response()->json([
                'message' => $resultado['message'] ?? 'No se encontraron datos para este documento.',
            ], 422);
        }

        // Para RUC se intenta completar razón social, empresa y dirección fiscal.
        if ($tipo === 'ruc') {
            $nombre = trim((string) Arr::get($data, 'nombre_o_razon_social', ''));
            $direccionRaw = Arr::get($data, 'direccion_completa') ?: Arr::get($data, 'direccion', '');
            $direccion = trim((string) $direccionRaw);

            return response()->json([
                'campos' => [
                    'nombre_completo' => $nombre,
                    'empresa' => $nombre !== '' ? $nombre : null,
                    'direccion' => $direccion !== '' ? $direccion : null,
                ],
                'info' => $this->sunatSummary($data),
            ]);
        }

        // Para DNI solo se completa el nombre completo de la persona.
        $nombreCompleto = trim((string) Arr::get($data, 'nombre_completo', ''));

        return response()->json([
            'campos' => [
                'nombre_completo' => $nombreCompleto,
            ],
            'info' => null,
        ]);
    }

    /**
     * Construye un resumen corto con el estado y la condición SUNAT.
     *
     * @param  array<string, mixed>  $data
     */
    private function sunatSummary(array $data): ?string
    {
        $estado = trim((string) Arr::get($data, 'estado', ''));
        $condicion = trim((string) Arr::get($data, 'condicion', ''));

        if ($estado === '' && $condicion === '') {
            return null;
        }

        if ($condicion !== '' && $estado !== '') {
            return 'SUNAT · '.$estado.' · '.$condicion;
        }

        return trim('SUNAT · '.$estado.$condicion);
    }
}
