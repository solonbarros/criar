<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Provide CRUD operations for departments (setores) via API.
 */
class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Department::with('agency')->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('visitors.manage'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'agency_id' => ['required', 'exists:agencies,id'],
            'floor' => ['nullable', 'string', 'max:50'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        $department = Department::create($data);

        return response()->json($department, 201);
    }
}
