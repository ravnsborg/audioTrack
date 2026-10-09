<?php

namespace App\Models;

use Database\Factories\KitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kit_number', 'status'])]
class Kit extends Model
{
    public const STATUSES = [
        'available' => 'Available',
        'staged' => 'Staged',
        'in_transit' => 'In Transit',
        'delivered' => 'Delivered',
        'pending_cleaning' => 'Pending Cleaning',
    ];

    /** @use HasFactory<KitFactory> */
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
