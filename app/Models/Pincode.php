<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    protected $fillable = [
        'code',
        'state_code',
        'state_name',
        'serial',
    ];

    protected function casts(): array
    {
        return [
            'serial' => 'integer',
        ];
    }
}
