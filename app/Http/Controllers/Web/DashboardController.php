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
        return view('dashboard', [
            'totalEmpleados' => Employee::count(),
            'empleadosActivos' => Employee::where('status', 'active')->count(),
            'totalTurnos' => EmployeeShift::count(),
            'atencionesAbiertas' => CustomerServiceRecord::whereIn(
                'status',
                ['open', 'in_progress']
            )->count(),
            'solicitudesPendientes' => ServiceRequest::where('status', 'pending')->count(),
            'ultimosEmpleados' => Employee::with('position')
                ->latest('created_at')
                ->take(5)
                ->get(),
        ]);
    }
}
