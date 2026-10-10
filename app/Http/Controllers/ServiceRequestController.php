<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = ServiceRequest::with(['employee', 'customerService'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => ['nullable', 'uuid', 'exists:employees,id'],
            'customer_service_id' => [
                'nullable',
                'uuid',
                'exists:customer_service_records,id',
            ],
            'request_type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'status' => [
                'sometimes',
                Rule::in(['pending', 'approved', 'rejected', 'completed']),
            ],
            'central_reference_id' => ['nullable', 'uuid'],
            'resolved_at' => ['nullable', 'date'],
        ]);

        $serviceRequest = ServiceRequest::create(array_merge(
            $validated,
            ['status' => $validated['status'] ?? 'pending']
        ));

        $serviceRequest->load(['employee', 'customerService']);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud registrada correctamente.',
            'data' => $serviceRequest,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => ['sometimes', 'nullable', 'uuid', 'exists:employees,id'],
            'customer_service_id' => [
                'sometimes',
                'nullable',
                'uuid',
                'exists:customer_service_records,id',
            ],
            'request_type' => ['sometimes', 'required', 'string', 'max:100'],
            'description' => ['sometimes', 'required', 'string'],
            'status' => [
                'sometimes',
                'required',
                Rule::in(['pending', 'approved', 'rejected', 'completed']),
            ],
            'central_reference_id' => ['sometimes', 'nullable', 'uuid'],
            'resolved_at' => ['sometimes', 'nullable', 'date'],
        ]);

        $serviceRequest->update($validated);
        $serviceRequest->load(['employee', 'customerService']);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud actualizada correctamente.',
            'data' => $serviceRequest,
        ]);
    }
}
