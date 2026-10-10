<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CustomerServiceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerServiceController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');

        $atenciones = CustomerServiceRecord::with('employee')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('service_type', 'ilike', "%{$buscar}%")
                        ->orWhere('description', 'ilike', "%{$buscar}%")
                        ->orWhereHas('employee', function ($employeeQuery) use ($buscar) {
                            $employeeQuery
                                ->where('full_name', 'ilike', "%{$buscar}%")
                                ->orWhere('employee_number', 'ilike', "%{$buscar}%");
                        });
                });
            })
            ->orderByRaw('attended_at DESC NULLS LAST')
            ->paginate(10)
            ->withQueryString();

        return view('services.index', compact('atenciones', 'buscar'));
    }

    public function create()
    {
        $empleados = Employee::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('services.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'employee_id' => [
                'required',
                'uuid',
                Rule::exists('employees', 'id')->where('status', 'active'),
            ],
            'service_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => [
                'required',
                Rule::in(['open', 'in_progress', 'closed']),
            ],
        ]);

        $datos['attended_at'] = now();
        $datos['closed_at'] = $datos['status'] === 'closed'
            ? now()
            : null;

        CustomerServiceRecord::create($datos);

        return redirect()
            ->route('services.index')
            ->with('success', 'Atención registrada correctamente.');
    }

    public function edit(CustomerServiceRecord $record)
    {
        $record->load('employee');

        $empleados = Employee::where('status', 'active')
            ->orWhere('id', $record->employee_id)
            ->orderBy('full_name')
            ->get();

        return view('services.edit', compact('record', 'empleados'));
    }

    public function update(Request $request, CustomerServiceRecord $record)
    {
        $datos = $request->validate([
            'employee_id' => [
                'required',
                'uuid',
                Rule::exists('employees', 'id')->where('status', 'active'),
            ],
            'service_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => [
                'required',
                Rule::in(['open', 'in_progress', 'closed']),
            ],
        ]);

        if ($datos['status'] === 'closed') {
            $datos['closed_at'] = $record->closed_at ?? now();
        } else {
            $datos['closed_at'] = null;
        }

        $record->update($datos);

        return redirect()
            ->route('services.index')
            ->with('success', 'Atención actualizada correctamente.');
    }
}
