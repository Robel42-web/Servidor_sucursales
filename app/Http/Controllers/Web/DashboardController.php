<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\CustomerServiceRecord;
use App\Models\ServiceRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $codigo = session('branch.codigo');
        abort_unless(is_string($codigo) && trim($codigo) !== '', 403, 'La sesión no tiene una sucursal válida.');
        $codigo = trim($codigo);

        return view('dashboard', [
            'totalEmpleados' => Employee::where('branch_code', $codigo)->count(),
            'empleadosActivos' => Employee::where('branch_code', $codigo)->where('status', 'active')->count(),
            'totalTurnos' => EmployeeShift::where('branch_code', $codigo)->count(),
            'atencionesAbiertas' => CustomerServiceRecord::where('branch_code', $codigo)
                ->whereIn('status', ['open', 'in_progress'])->count(),
            'solicitudesPendientes' => ServiceRequest::where('branch_code', $codigo)
                ->where('status', 'pending')->count(),
            'ultimosEmpleados' => Employee::where('branch_code', $codigo)
                ->with('position')->latest('created_at')->take(5)->get(),
        ]);
    }
}
