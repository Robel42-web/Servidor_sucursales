<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerServiceRecord;
use App\Models\Employee;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceRequestController extends Controller
{
    private const STATUSES = [
        'pending',
        'approved',
        'rejected',
        'completed',
    ];

    public function index(Request $request)
    {
        $query = ServiceRequest::with(['employee', 'customerService'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('request_type', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $solicitudes = $query->paginate(10)->withQueryString();

        $totalSolicitudes = ServiceRequest::count();
        $pendientes = ServiceRequest::where('status', 'pending')->count();
        $completadas = ServiceRequest::where('status', 'completed')->count();

        return view('requests.index', compact(
            'solicitudes',
            'totalSolicitudes',
            'pendientes',
            'completadas'
        ));
    }

    public function create()
    {
        $empleados = Employee::orderBy('full_name')->get();

        $atenciones = CustomerServiceRecord::orderByDesc('attended_at')
            ->get();

        return view('requests.create', compact('empleados', 'atenciones'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'employee_id' => [
                'nullable',
                'uuid',
                Rule::exists('employees', 'id'),
            ],
            'customer_service_id' => [
                'nullable',
                'uuid',
                Rule::exists('customer_service_records', 'id'),
            ],
            'request_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'central_reference_id' => ['nullable', 'uuid'],
            'resolved_at' => ['nullable', 'date'],
        ]);

        $datos['created_at'] = now();

        if ($datos['status'] === 'completed' && empty($datos['resolved_at'])) {
            $datos['resolved_at'] = now();
        }

        ServiceRequest::create($datos);

        return redirect()
            ->route('requests.index')
            ->with('success', 'Solicitud registrada correctamente.');
    }

    public function edit(string $id)
    {
        $solicitud = ServiceRequest::findOrFail($id);

        $empleados = Employee::orderBy('full_name')->get();

        $atenciones = CustomerServiceRecord::orderByDesc('attended_at')
            ->get();

        return view('requests.edit', compact(
            'solicitud',
            'empleados',
            'atenciones'
        ));
    }

    public function update(Request $request, string $id)
    {
        $solicitud = ServiceRequest::findOrFail($id);

        $datos = $request->validate([
            'employee_id' => [
                'nullable',
                'uuid',
                Rule::exists('employees', 'id'),
            ],
            'customer_service_id' => [
                'nullable',
                'uuid',
                Rule::exists('customer_service_records', 'id'),
            ],
            'request_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'central_reference_id' => ['nullable', 'uuid'],
            'resolved_at' => ['nullable', 'date'],
        ]);

        if ($datos['status'] === 'completed' && empty($datos['resolved_at'])) {
            $datos['resolved_at'] = $solicitud->resolved_at ?? now();
        }

        $solicitud->update($datos);

        return redirect()
            ->route('requests.index')
            ->with('success', 'Solicitud actualizada correctamente.');
    }
}
