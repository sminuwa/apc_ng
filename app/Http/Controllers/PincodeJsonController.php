<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\JsonResponse;

class PincodeJsonController extends Controller
{
    /**
     * Full pincode list as JSON: [{ id, code, state_name, serial }, ...]
     */
    public function __invoke(): JsonResponse
    {
        $rows = Pincode::query()
            ->orderBy('id')
            ->get(['id', 'code', 'state_name', 'serial']);

        return response()->json($rows);
    }
}
