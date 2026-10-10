<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkShift extends Model
{
    use HasUuids;

    protected $table = 'work_shifts';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'description',
    ];

    public function employeeShifts(): HasMany
    {
        return $this->hasMany(EmployeeShift::class, 'shift_id');
    }
}
