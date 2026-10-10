<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    // GET: listar empleados
    public function index(): JsonResponse
    {
        $employees = Employee::with('position')
            ->orderBy('full_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

    // GET: consultar un empleado
    public function show(string $id): JsonResponse
    {
        $employee = Employee::with('position')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    // POST: registrar empleado
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_number' => [
                'required', 'string', 'max:30',
                'unique:employees,employee_number',
            ],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:255',
                'unique:employees,email',
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_id' => [
                'nullable', 'uuid', 'exists:job_positions,id',
            ],
            'hire_date' => ['sometimes', 'required', 'date'],
        ]);

        $employee = Employee::create(
            array_merge($validated, ['status' => 'active'])
        );

        $employee->load('position');

        return response()->json([
            'success' => true,
            'message' => 'Empleado registrado correctamente.',
            'data' => $employee,
        ], 201);
    }

    // PUT: actualizar empleado
    public function update(Request $request, string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'employee_number' => [
                'sometimes', 'required', 'string', 'max:30',
                Rule::unique('employees', 'employee_number')
                    ->ignore($employee->id, 'id'),
            ],
            'full_name' => ['sometimes', 'required', 'string', 'max:150'],
            'email' => [
                'sometimes', 'required', 'email', 'max:255',
                Rule::unique('employees', 'email')
                    ->ignore($employee->id, 'id'),
            ],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'position_id' => [
                'sometimes', 'nullable', 'uuid',
                'exists:job_positions,id',
            ],
            'hire_date' => ['sometimes', 'required', 'date'],
            'status' => [
                'sometimes', 'required',
                'in:active,inactive,suspended',
            ],
        ]);

        $employee->update($validated);
        $employee->load('position');

        return response()->json([
            'success' => true,
            'message' => 'Empleado actualizado correctamente.',
            'data' => $employee,
        ]);
    }

    // DELETE: eliminar empleado
    public function destroy(string $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        try {
            $employee->delete();
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el empleado porque tiene registros relacionados.',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Empleado eliminado correctamente.',
        ]);
    }
}