<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created');
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted');
    }

    private function record(Model $model, string $action): void
    {
        $employeeId = $model->getAttribute('employee_id');

        AuditLog::create([
            'employee_id' => $employeeId,
            'action' => $action,
            'entity_type' => $model->getTable(),
            'entity_reference' => (string) $model->getKey(),
            'details' => [
                'operation' => $action,
                'entity' => $model->getTable(),
                'attributes' => $model->getAttributes(),
            ],
        ]);
    }
}
