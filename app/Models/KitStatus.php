<?php

namespace App\Models;

use Database\Factories\KitStatusFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class KitStatus extends Model
{
    /** @use HasFactory<KitStatusFactory> */
    use HasFactory;

    public function kits(): HasMany
    {
        return $this->hasMany(Kit::class);
    }
}
