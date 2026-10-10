<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerServiceRecord extends Model
{
    use HasUuids;

    protected $table = 'customer_service_records';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'central_account_id',
        'service_type',
        'description',
        'status',
        'attended_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
            'closed_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'customer_service_id');
    }
}
