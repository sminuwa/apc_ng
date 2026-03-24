<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\JsonResponse;

class PincodeJsonController extends Controller
{
    /**
     * Full pincode list: [{ id, code, state_name, serial, status }, ...]
     * status: 0 = used, 1 = valid (only stored rows; invalid = 2 is not listed here).
     */
    public function __invoke(): JsonResponse
    {
        $rows = Pincode::query()
            ->orderBy('id')
            ->get(['id', 'code', 'state_name', 'serial', 'status']);

        return response()->json($rows);
    }
}
