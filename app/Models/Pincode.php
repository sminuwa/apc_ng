<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    /** Stored in DB: already scanned via push */
    public const STATUS_USED = 0;

    /** Stored in DB: not yet scanned / available */
    public const STATUS_VALID = 1;

    /** Not stored in DB — API only when the pincode does not exist */
    public const STATUS_INVALID = 2;

    protected $fillable = [
        'code',
        'state_code',
        'state_name',
        'serial',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'serial' => 'integer',
            'status' => 'integer',
        ];
    }
}
