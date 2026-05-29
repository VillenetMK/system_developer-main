<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CalendarioController extends Controller
{
    public function index(Request $request): Response
    {
        $monthParam = (string) $request->query('month', now()->format('Y-m'));
        $estadoParam = (string) $request->query('estado', 'todos');
        $monthDate = now()->startOfMonth();

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

        $visitasQuery = Visita::query()
            ->with('cliente')
            ->whereBetween('dia', [$monthStart->toDateString(), $monthEnd->toDateString()]);

        if ($selectedEstado !== 'todos') {
            $visitasQuery->where('estado', $selectedEstado);
        }

        $visitasByDate = $visitasQuery
            ->orderBy('dia')
            ->orderBy('hora_estimada')
            ->get()
            ->groupBy(fn (Visita $visita) => $visita->dia->toDateString());

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
            'weekDays' => ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
            'weeks' => $weeks,
            'selectedEstado' => $selectedEstado,
        ]);
    }
}
