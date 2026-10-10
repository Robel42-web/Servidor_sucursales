<?php

namespace App\Http\Controllers;

use App\Models\CustomerServiceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerServiceRecordController extends Controller
{
    public function index(): JsonResponse
    {
        $records = CustomerServiceRecord::with('employee')
            ->orderByDesc('attended_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => ['nullable', 'uuid', 'exists:employees,id'],
            'central_account_id' => ['nullable', 'uuid'],
            'service_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'status' => [
                'sometimes',
                Rule::in(['open', 'in_progress', 'completed', 'cancelled']),
            ],
            'attended_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date', 'after_or_equal:attended_at'],
        ]);

        $record = CustomerServiceRecord::create(array_merge(
            $validated,
            ['status' => $validated['status'] ?? 'open']
        ));

        $record->load('employee');

        return response()->json([
            'success' => true,
            'message' => 'Atención registrada correctamente.',
            'data' => $record,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $record = CustomerServiceRecord::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => ['sometimes', 'nullable', 'uuid', 'exists:employees,id'],
            'central_account_id' => ['sometimes', 'nullable', 'uuid'],
            'service_type' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'required', 'string'],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['open', 'in_progress', 'completed', 'cancelled']),
            ],
            'attended_at' => ['sometimes', 'nullable', 'date'],
            'closed_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:attended_at'],
        ]);

        $record->update($validated);
        $record->load('employee');

        return response()->json([
            'success' => true,
            'message' => 'Atención actualizada correctamente.',
            'data' => $record,
        ]);
    }
}
