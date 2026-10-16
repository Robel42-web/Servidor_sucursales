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
        $codigo = $this->branchCode();
        $buscar = $request->query('buscar');

        $atenciones = CustomerServiceRecord::where('branch_code', $codigo)
            ->with('employee')
            ->when($buscar, function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('service_type', 'ilike', "%{$buscar}%")
                        ->orWhere('description', 'ilike', "%{$buscar}%")
                        ->orWhereHas('employee', function ($employeeQuery) use ($buscar) {
                            $employeeQuery->where('full_name', 'ilike', "%{$buscar}%")
                                ->orWhere('employee_number', 'ilike', "%{$buscar}%");
                        });
                });
            })
            ->orderByRaw('attended_at DESC NULLS LAST')
            ->paginate(10)->withQueryString();

        return view('services.index', compact('atenciones', 'buscar'));
    }

    public function create()
    {
        $empleados = Employee::where('branch_code', $this->branchCode())
            ->where('status', 'active')->orderBy('full_name')->get();
        return view('services.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $codigo = $this->branchCode();
        $datos = $request->validate([
            'employee_id' => [
                'required', 'uuid',
                Rule::exists('employees', 'id')->where('status', 'active')->where('branch_code', $codigo),
            ],
            'service_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'closed'])],
        ]);

        $datos['branch_code'] = $codigo;
        $datos['attended_at'] = now();
        $datos['closed_at'] = $datos['status'] === 'closed' ? now() : null;
        CustomerServiceRecord::create($datos);

        return redirect()->route('services.index')->with('success', 'Atención registrada correctamente.');
    }

    public function edit(CustomerServiceRecord $record)
    {
        $record = $this->findOwnRecord($record);
        $record->load('employee');
        $codigo = $this->branchCode();
        $empleados = Employee::where('branch_code', $codigo)
            ->where(function ($query) use ($record) {
                $query->where('status', 'active')->orWhere('id', $record->employee_id);
            })->orderBy('full_name')->get();

        return view('services.edit', compact('record', 'empleados'));
    }

    public function update(Request $request, CustomerServiceRecord $record)
    {
        $record = $this->findOwnRecord($record);
        $codigo = $this->branchCode();
        $datos = $request->validate([
            'employee_id' => [
                'required', 'uuid',
                Rule::exists('employees', 'id')->where('status', 'active')->where('branch_code', $codigo),
            ],
            'service_type' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'closed'])],
        ]);

        $datos['closed_at'] = $datos['status'] === 'closed'
            ? ($record->closed_at ?? now())
            : null;
        $record->update($datos);

        return redirect()->route('services.index')->with('success', 'Atención actualizada correctamente.');
    }

    private function branchCode(): string
    {
        $codigo = session('branch.codigo');
        abort_unless(is_string($codigo) && trim($codigo) !== '', 403, 'La sesión no tiene una sucursal válida.');
        return trim($codigo);
    }

    private function findOwnRecord(CustomerServiceRecord $record): CustomerServiceRecord
    {
        return CustomerServiceRecord::where('branch_code', $this->branchCode())
            ->whereKey($record->getKey())->firstOrFail();
    }
}
