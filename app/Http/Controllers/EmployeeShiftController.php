<?php

namespace App\Http\Controllers;

use App\Models\EmployeeShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeShiftController extends Controller
{
    public function index(): JsonResponse
    {
        $assignments = EmployeeShift::with(['employee', 'shift'])
            ->orderByDesc('work_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'shift_id' => ['required', 'uuid', 'exists:work_shifts,id'],
            'work_date' => [
                'required',
                'date',
                Rule::unique('employee_shifts', 'work_date')
                    ->where('employee_id', $request->input('employee_id')),
            ],
            'status' => ['sometimes', 'in:scheduled,completed,absent'],
        ]);

        $assignment = EmployeeShift::create(array_merge(
            $validated,
            ['status' => $validated['status'] ?? 'scheduled']
        ));

        $assignment->load(['employee', 'shift']);

        return response()->json([
            'success' => true,
            'message' => 'Turno asignado correctamente.',
            'data' => $assignment,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $assignment = EmployeeShift::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => ['sometimes', 'required', 'uuid', 'exists:employees,id'],
            'shift_id' => ['sometimes', 'required', 'uuid', 'exists:work_shifts,id'],
            'work_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', 'in:scheduled,completed,absent'],
        ]);

        $employeeId = $validated['employee_id'] ?? $assignment->employee_id;
        $workDate = $validated['work_date'] ?? $assignment->work_date;

        $duplicate = EmployeeShift::where('employee_id', $employeeId)
            ->where('work_date', $workDate)
            ->where('id', '!=', $assignment->id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado ya tiene una asignación para esa fecha.',
            ], 422);
        }

        $assignment->update($validated);
        $assignment->load(['employee', 'shift']);

        return response()->json([
            'success' => true,
            'message' => 'Asignación actualizada correctamente.',
            'data' => $assignment,
        ]);
    }
}
