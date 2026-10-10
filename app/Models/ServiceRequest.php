<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasUuids;

    protected $table = 'service_requests';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'employee_id',
        'customer_service_id',
        'request_type',
        'description',
        'status',
        'central_reference_id',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(
            CustomerServiceRecord::class,
            'customer_service_id'
        );
    }
}
