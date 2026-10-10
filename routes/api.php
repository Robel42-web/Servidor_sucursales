<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WorkShiftController;
use App\Http\Controllers\EmployeeShiftController;
use App\Http\Controllers\CustomerServiceRecordController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\AuditLogController;

Route::middleware('api.key')->group(function () {

    // Puestos
    Route::get('/job-positions', [JobPositionController::class, 'index']);

    // Empleados
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/employees/{id}', [EmployeeController::class, 'show']);
    Route::post('/employees', [EmployeeController::class, 'store']);
    Route::put('/employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

    // Turnos
    Route::get('/work-shifts', [WorkShiftController::class, 'index']);

    // Asignaciones de turnos
    Route::get('/employee-shifts', [EmployeeShiftController::class, 'index']);
    Route::post('/employee-shifts', [EmployeeShiftController::class, 'store']);
    Route::put('/employee-shifts/{id}', [EmployeeShiftController::class, 'update']);

    // Atención a clientes
    Route::get('/customer-service-records', [CustomerServiceRecordController::class, 'index']);
    Route::post('/customer-service-records', [CustomerServiceRecordController::class, 'store']);
    Route::put('/customer-service-records/{id}', [CustomerServiceRecordController::class, 'update']);

    // Solicitudes de servicio
    Route::get('/service-requests', [ServiceRequestController::class, 'index']);
    Route::post('/service-requests', [ServiceRequestController::class, 'store']);
    Route::put('/service-requests/{id}', [ServiceRequestController::class, 'update']);

    // Auditoría: solo consulta
    Route::get('/audit-logs', [AuditLogController::class, 'index']);
});
