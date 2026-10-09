<?php

namespace App\Models;

use Database\Factories\KitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kit_number', 'kit_status_id'])]
class Kit extends Model
{
    /** @use HasFactory<KitFactory> */
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function kitStatus(): BelongsTo
    {
        return $this->belongsTo(KitStatus::class);
    }
}
