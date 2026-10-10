<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\ShiftController;
use App\Http\Controllers\Web\CustomerServiceController;
use App\Http\Controllers\Web\ServiceRequestController;


Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/empleados', [EmployeeController::class, 'index'])
    ->name('employees.index');

Route::get('/empleados/crear', [EmployeeController::class, 'create'])
    ->name('employees.create');

Route::post('/empleados', [EmployeeController::class, 'store'])
    ->name('employees.store');

Route::get('/empleados/{employee}/editar', [EmployeeController::class, 'edit'])
    ->name('employees.edit');

Route::put('/empleados/{employee}', [EmployeeController::class, 'update'])
    ->name('employees.update');

Route::patch('/empleados/{employee}/baja', [EmployeeController::class, 'deactivate'])
    ->name('employees.deactivate');

Route::get('/turnos', [ShiftController::class, 'index'])
    ->name('shifts.index');

Route::get('/turnos/crear', [ShiftController::class, 'create'])
    ->name('shifts.create');

Route::post('/turnos', [ShiftController::class, 'store'])
    ->name('shifts.store');

Route::get('/turnos/{shift}/editar', [ShiftController::class, 'edit'])
    ->name('shifts.edit');

Route::put('/turnos/{shift}', [ShiftController::class, 'update'])
    ->name('shifts.update');

Route::get('/atenciones', [CustomerServiceController::class, 'index'])
    ->name('services.index');

Route::get('/atenciones/crear', [CustomerServiceController::class, 'create'])
    ->name('services.create');

Route::post('/atenciones', [CustomerServiceController::class, 'store'])
    ->name('services.store');

Route::get('/atenciones/{record}/editar', [CustomerServiceController::class, 'edit'])
    ->name('services.edit');

Route::put('/atenciones/{record}', [CustomerServiceController::class, 'update'])
    ->name('services.update');

Route::get('/solicitudes', [ServiceRequestController::class, 'index'])
    ->name('requests.index');

Route::get('/solicitudes/crear', [ServiceRequestController::class, 'create'])
    ->name('requests.create');

Route::post('/solicitudes', [ServiceRequestController::class, 'store'])
    ->name('requests.store');

Route::get('/solicitudes/{id}/editar', [ServiceRequestController::class, 'edit'])
    ->name('requests.edit');

Route::put('/solicitudes/{id}', [ServiceRequestController::class, 'update'])
    ->name('requests.update');
