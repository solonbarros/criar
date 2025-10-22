<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Generate analytical reports for administrators.
 */
class ReportController extends Controller
{
    /**
     * Return aggregated access information grouped by department and day.
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = DB::table('visitor_accesses')
            ->selectRaw('DATE(entry_at) as day, departments.name as department_name, COUNT(*) as total')
            ->join('departments', 'visitor_accesses.department_id', '=', 'departments.id')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('entry_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('entry_at', '<=', $request->date('to')))
            ->groupBy('day', 'departments.name')
            ->orderByDesc('day')
            ->get();

        return response()->json($query);
    }
}
