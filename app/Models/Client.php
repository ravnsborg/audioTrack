<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'primary_contact_name', 'phone', 'email', 'address'])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;
}
