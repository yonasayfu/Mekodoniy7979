<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreSponsorship extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'relationship_type',
    ];
}
