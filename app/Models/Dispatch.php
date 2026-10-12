<?php

namespace App\Models;

use Database\Factories\DispatchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['driver_id', 'client_location_id', 'kit_id', 'status', 'queue_position', 'delivery_datetime'])]
#[Hidden(['token'])]
class Dispatch extends Model
{
    public const STATUSES = [
        'staged' => 'Staged',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
    ];

    /** @use HasFactory<DispatchFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(static function (Dispatch $dispatch): void {
            if (blank($dispatch->token)) {
                $dispatch->token = Str::random(64);
            }
        });
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function clientLocation(): BelongsTo
    {
        return $this->belongsTo(ClientLocation::class);
    }

    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
    }

    protected function casts(): array
    {
        return [
            'queue_position' => 'integer',
            'delivery_datetime' => 'datetime',
        ];
    }
}
