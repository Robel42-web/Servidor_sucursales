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
        $codigo = $this->branchCode();
        $busqueda = $request->string('buscar')->trim()->toString();

        $empleados = Employee::where('branch_code', $codigo)
            ->with('position')
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
        $codigo = $this->branchCode();
        $datos = $request->validate([
            'employee_number' => ['required', 'string', 'max:50', 'unique:employees,employee_number'],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_id' => ['required', 'uuid', 'exists:job_positions,id'],
            'hire_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
        ]);

        // La sucursal viene de la sesión autenticada, nunca del formulario.
        $datos['branch_code'] = $codigo;
        Employee::create($datos);

        return redirect()->route('employees.index')->with('success', 'Empleado registrado correctamente.');
    }

    public function edit(Employee $employee)
    {
        $employee = $this->findOwnEmployee($employee);

        return view('employees.edit', [
            'empleado' => $employee,
            'puestos' => JobPosition::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $employee = $this->findOwnEmployee($employee);
        $datos = $request->validate([
            'employee_number' => [
                'required', 'string', 'max:50',
                Rule::unique('employees', 'employee_number')->ignore($employee->id, 'id'),
            ],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('employees', 'email')->ignore($employee->id, 'id'),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'position_id' => ['required', 'uuid', 'exists:job_positions,id'],
            'hire_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
        ]);

        // Nunca se permite modificar branch_code mediante el formulario.
        $employee->update($datos);

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function deactivate(Employee $employee)
    {
        $employee = $this->findOwnEmployee($employee);

        if ($employee->status === 'inactive') {
            return redirect()->route('employees.index')->with('success', 'El empleado ya está dado de baja.');
        }

        $employee->update(['status' => 'inactive']);

        return redirect()->route('employees.index')->with('success', 'Empleado dado de baja correctamente.');
    }

    private function branchCode(): string
    {
        $codigo = session('branch.codigo');
        abort_unless(is_string($codigo) && trim($codigo) !== '', 403, 'La sesión no tiene una sucursal válida.');
        return trim($codigo);
    }

    private function findOwnEmployee(Employee $employee): Employee
    {
        return Employee::where('branch_code', $this->branchCode())
            ->whereKey($employee->getKey())
            ->firstOrFail();
    }
}
