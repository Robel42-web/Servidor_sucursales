<?php

namespace App\Providers;

use App\Models\Employee;
use App\Models\EmployeeShift;
use App\Models\CustomerServiceRecord;
use App\Models\ServiceRequest;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Employee::observe(AuditObserver::class);
        EmployeeShift::observe(AuditObserver::class);
        CustomerServiceRecord::observe(AuditObserver::class);
        ServiceRequest::observe(AuditObserver::class);
    }
}
