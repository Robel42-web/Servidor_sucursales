<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\JobPosition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->string('buscar')->trim()->toString();

        $empleados = Employee::with('position')
            ->when($busqueda !== '', function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery
                        ->where('full_name', 'ilike', "%{$busqueda}%")
                        ->orWhere('employee_number', 'ilike', "%{$busqueda}%")
                        ->orWhere('email', 'ilike', "%{$busqueda}%");
                });
            })
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', [
            'empleados' => $empleados,
            'buscar' => $busqueda,
        ]);
    }

    public function create()
    {
        return view('employees.create', [
            'puestos' => JobPosition::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'employee_number' => [
                'required', 'string', 'max:50',
                'unique:employees,employee_number',
            ],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:255',
                'unique:employees,email',
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_id' => [
                'required', 'uuid',
                'exists:job_positions,id',
            ],
            'hire_date' => ['required', 'date'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'suspended']),
            ],
        ]);

        Employee::create($datos);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'empleado' => $employee,
            'puestos' => JobPosition::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $datos = $request->validate([
            'employee_number' => [
                'required', 'string', 'max:50',
                Rule::unique('employees', 'employee_number')
                    ->ignore($employee->id, 'id'),
            ],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('employees', 'email')
                    ->ignore($employee->id, 'id'),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_id' => [
                'required', 'uuid',
                'exists:job_positions,id',
            ],
            'hire_date' => ['required', 'date'],
            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'suspended']),
            ],
        ]);

        $employee->update($datos);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function deactivate(Employee $employee)
    {
        if ($employee->status === 'inactive') {
            return redirect()
                ->route('employees.index')
                ->with('success', 'El empleado ya está dado de baja.');
        }

        $employee->update(['status' => 'inactive']);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Empleado dado de baja correctamente.');
    }
}
