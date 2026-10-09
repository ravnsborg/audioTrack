<?php

namespace App\Models;

use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kit_id', 'sku', 'serial_number', 'name', 'category_id', 'notes'])]
class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function kit(): BelongsTo
    {
        return $this->belongsTo(Kit::class);
    }
}
