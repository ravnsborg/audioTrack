<?php

namespace App\Models;

use Database\Factories\ItemStatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class ItemStatus extends Model
{
    /** @use HasFactory<ItemStatusFactory> */
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'items_status_id');
    }
}
