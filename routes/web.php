<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\ShiftController;
use App\Http\Controllers\Web\CustomerServiceController;
use App\Http\Controllers\Web\ServiceRequestController;
use App\Http\Controllers\Web\BankingController;
use App\Http\Controllers\Web\BranchLoginController;

// Rutas públicas de autenticación
Route::get('/login', [BranchLoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [BranchLoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.submit');

// Primera vinculación de la API Key de la sucursal.
Route::get('/login/sucursal/api-key', [BranchLoginController::class, 'showBranchKeyForm'])
    ->name('branch.key.form');
Route::post('/login/sucursal/api-key', [BranchLoginController::class, 'submitBranchKey'])
    ->middleware('throttle:5,1')
    ->name('branch.key.submit');

// Cerrar sesión requiere una sesión autenticada
Route::post('/logout', [BranchLoginController::class, 'logout'])
    ->middleware('branch.auth')
    ->name('logout');

// Operaciones bancarias: se consumen desde la sucursal autenticada.
Route::middleware('branch.auth')->group(function () {
    Route::get('/cuentas', [BankingController::class, 'accounts'])->name('bank.accounts.index');
    Route::post('/cuentas', [BankingController::class, 'storeAccount'])->middleware('throttle:10,1')->name('bank.accounts.store');
    Route::get('/operaciones', [BankingController::class, 'operations'])->name('bank.operations.index');
    Route::post('/operaciones/depositar', [BankingController::class, 'deposit'])->middleware('throttle:10,1')->name('bank.operations.deposit');
    Route::post('/operaciones/retirar', [BankingController::class, 'withdraw'])->middleware('throttle:10,1')->name('bank.operations.withdraw');
});

// Panel protegido de la sucursal
Route::middleware('branch.auth')->group(function () {

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
});
