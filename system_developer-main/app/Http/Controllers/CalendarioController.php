<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarioController extends Controller
{
    /**
     * Muestra el calendario mensual de visitas.
     *
     * Permite filtrar por mes y por estado de visita. Si los parámetros no son
     * válidos, se usan valores seguros por defecto.
     */
    public function index(Request $request): Response
    {
        $monthParam = (string) $request->query('month', now()->format('Y-m'));
        $estadoParam = (string) $request->query('estado', 'todos');
        $monthDate = now()->startOfMonth();

        // Valida el formato YYYY-MM antes de intentar convertirlo a fecha.
        if (preg_match('/^\d{4}-\d{2}$/', $monthParam) === 1) {
            $parsedMonth = Carbon::createFromFormat('Y-m', $monthParam);
            if ($parsedMonth !== false) {
                $monthDate = $parsedMonth->startOfMonth();
            }
        }

        $monthStart = $monthDate->copy()->startOfMonth();
        $monthEnd = $monthDate->copy()->endOfMonth();

        $estadoOptions = ['todos', 'en proceso', 'cancelada', 'culminada'];
        $selectedEstado = in_array($estadoParam, $estadoOptions, true) ? $estadoParam : 'todos';

        // Consulta las visitas del mes seleccionado e incluye el cliente relacionado.
        $visitasQuery = Visita::query()
            ->with('cliente')
            ->whereBetween('dia', [$monthStart->toDateString(), $monthEnd->toDateString()]);

        if ($selectedEstado !== 'todos') {
            $visitasQuery->where('estado', $selectedEstado);
        }

        // Agrupa las visitas por fecha para mostrarlas en la celda del calendario.
        $visitasByDate = $visitasQuery
            ->orderBy('dia')
            ->orderBy('hora_estimada')
            ->get()
            ->groupBy(fn (Visita $visita) => $visita->dia->toDateString());

        // Se empieza en lunes y se termina en domingo para formar semanas completas.
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $weeks = [];
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $week = [];

            for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                $dateKey = $cursor->toDateString();
                $week[] = [
                    'date' => $cursor->copy(),
                    'is_current_month' => $cursor->isSameMonth($monthDate),
                    'is_today' => $cursor->isToday(),
                    'visitas' => $visitasByDate->get($dateKey, collect()),
                ];
                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return response()->view('calendario.index', [
            'monthDate' => $monthDate,
            'prevMonth' => $monthDate->copy()->subMonthNoOverflow()->format('Y-m'),
            'nextMonth' => $monthDate->copy()->addMonthNoOverflow()->format('Y-m'),
            'weekDays' => ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            'weeks' => $weeks,
            'selectedEstado' => $selectedEstado,
        ]);
    }
}
