<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ApiPeruDevService
{
    /**
     * Consulta los datos de una persona natural por DNI.
     *
     * @return array{success: bool, data: ?array<string, mixed>, message: ?string}
     */
    public function consultarDni(string $dni): array
    {
        return $this->post(['dni' => $dni], 'api/dni');
    }

    /**
     * Consulta los datos de una persona o empresa por RUC.
     *
     * @return array{success: bool, data: ?array<string, mixed>, message: ?string}
     */
    public function consultarRuc(string $ruc): array
    {
        return $this->post(['ruc' => $ruc], 'api/ruc');
    }

    /**
     * Envía la petición HTTP a ApiPeru y normaliza la respuesta.
     *
     * El método devuelve siempre la misma estructura para que los controladores
     * no dependan directamente del formato exacto de la API externa.
     *
     * @param  array<string, string|int|float|null>  $payload
     * @return array{success: bool, data: ?array<string, mixed>, message: ?string}
     */
    private function post(array $payload, string $endpoint): array
    {
        $token = (string) config('services.apiperu.token');

        if ($token === '') {
            return ['success' => false, 'data' => null, 'message' => 'La API ApiPeru no está configurada (APIPERU_DEV_TOKEN).'];
        }

        $base = rtrim((string) config('services.apiperu.base_url', 'https://apiperu.dev'), '/');
        $url = $base.'/'.ltrim($endpoint, '/');
        $verifySsl = filter_var(config('services.apiperu.verify_ssl'), FILTER_VALIDATE_BOOLEAN);

        try {
            $request = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.apiperu.timeout', 25))
                ->connectTimeout((int) config('services.apiperu.connect_timeout', 12));

            // Permite desactivar SSL en entornos locales donde el certificado falle.
            if (! $verifySsl) {
                $request = $request->withOptions(['verify' => false]);
            }

            $response = $request->post($url, $payload);

            if (! $response->successful()) {
                $decoded = $response->json();

                return [
                    'success' => false,
                    'data' => null,
                    'message' => is_array($decoded) ? ($decoded['message'] ?? $response->body()) : $response->body(),
                ];
            }

            $decoded = $response->json();
            $success = (bool) data_get($decoded, 'success', false);

            /** @var ?array<string, mixed> $data */
            $data = data_get($decoded, 'data');

            return [
                'success' => $success,
                'data' => is_array($data) ? $data : null,
                'message' => is_string(data_get($decoded, 'message')) ? data_get($decoded, 'message') : null,
            ];
        } catch (Throwable $e) {
            // Se registra el error técnico, pero al usuario se le muestra un mensaje simple.
            Log::warning('ApiPeru error: '.$e->getMessage());

            return ['success' => false, 'data' => null, 'message' => 'No se pudo conectar con ApiPeru. Intenta de nuevo.'];
        }
    }
}
