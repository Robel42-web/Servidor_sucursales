<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->query('buscar');

        $turnos = EmployeeShift::with(['employee', 'shift'])
            ->when($buscar, function ($query, $buscar) {
                $query->whereHas('employee', function ($employeeQuery) use ($buscar) {
                    $employeeQuery->where('full_name', 'ilike', "%{$buscar}%")
                        ->orWhere('employee_number', 'ilike', "%{$buscar}%");
                });
            })
            ->orderByDesc('work_date')
            ->paginate(10)
            ->withQueryString();

        return view('shifts.index', compact('turnos', 'buscar'));
    }

    public function create()
    {
        $empleados = Employee::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $horarios = WorkShift::orderBy('start_time')->get();

        return view('shifts.create', compact('empleados', 'horarios'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'employee_id' => [
                'required',
                'uuid',
                Rule::exists('employees', 'id')->where('status', 'active'),
            ],
            'shift_id' => ['required', 'uuid', 'exists:work_shifts,id'],
            'work_date' => ['required', 'date'],
            'status' => [
                'required',
                Rule::in(['scheduled', 'completed', 'absent']),
            ],
        ]);

        $existe = EmployeeShift::where('employee_id', $datos['employee_id'])
            ->where('work_date', $datos['work_date'])
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'employee_id' => 'Este empleado ya tiene un turno asignado para esa fecha.',
                ]);
        }

        EmployeeShift::create($datos);

        return redirect()
            ->route('shifts.index')
            ->with('success', 'Turno asignado correctamente.');
    }

    public function edit(EmployeeShift $shift)
    {
        $shift->load(['employee', 'shift']);

        $empleados = Employee::where('status', 'active')
            ->orWhere('id', $shift->employee_id)
            ->orderBy('full_name')
            ->get();

        $horarios = WorkShift::orderBy('start_time')->get();

        return view('shifts.edit', compact('shift', 'empleados', 'horarios'));
    }

    public function update(Request $request, EmployeeShift $shift)
    {
        $datos = $request->validate([
            'employee_id' => [
                'required',
                'uuid',
                Rule::exists('employees', 'id')->where('status', 'active'),
            ],
            'shift_id' => ['required', 'uuid', 'exists:work_shifts,id'],
            'work_date' => ['required', 'date'],
            'status' => [
                'required',
                Rule::in(['scheduled', 'completed', 'absent']),
            ],
        ]);

        $existe = EmployeeShift::where('employee_id', $datos['employee_id'])
            ->where('work_date', $datos['work_date'])
            ->where('id', '!=', $shift->id)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors([
                    'employee_id' => 'Este empleado ya tiene un turno asignado para esa fecha.',
                ]);
        }

        $shift->update($datos);

        return redirect()
            ->route('shifts.index')
            ->with('success', 'Turno actualizado correctamente.');
    }
}
