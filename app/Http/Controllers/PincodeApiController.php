<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PincodeApiController extends Controller
{
    /**
     * Return full pincode details by code (GET query or POST body: pincode).
     *
     * status: 0 = used, 1 = valid, 2 = invalid (not found).
     */
    public function fetch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pincode' => ['required', 'string', 'max:32'],
        ]);

        $code = $this->normalizePincode($validated['pincode']);

        $pin = Pincode::query()->where('code', $code)->first();

        if ($pin === null) {
            return response()->json([
                'status' => Pincode::STATUS_INVALID,
                'message' => 'Pincode not found.',
                'pincode' => $code,
            ], 404);
        }

        return response()->json($this->pincodePayload($pin));
    }

    /**
     * Mark pincode as used. Body: pincode.
     *
     * status: 0 on success or already used; 2 if not found.
     */
    public function push(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pincode' => ['required', 'string', 'max:32'],
        ]);

        $code = $this->normalizePincode($validated['pincode']);

        $pin = Pincode::query()->where('code', $code)->first();

        if ($pin === null) {
            return response()->json([
                'status' => Pincode::STATUS_INVALID,
                'message' => 'Pincode not found.',
                'pincode' => $code,
            ], 404);
        }

        if ((int) $pin->status === Pincode::STATUS_USED) {
            return response()->json([
                'message' => 'Pincode was already used.',
                ...$this->pincodePayload($pin),
            ]);
        }

        $pin->status = Pincode::STATUS_USED;
        $pin->save();

        return response()->json([
            'message' => 'Pincode marked as used.',
            ...$this->pincodePayload($pin->fresh()),
        ]);
    }

    private function normalizePincode(string $raw): string
    {
        return strtoupper(trim($raw));
    }

    /**
     * @return array<string, mixed>
     */
    private function pincodePayload(Pincode $pin): array
    {
        return [
            'status' => (int) $pin->status,
            'id' => $pin->id,
            'code' => $pin->code,
            'state_code' => $pin->state_code,
            'state_name' => $pin->state_name,
            'serial' => $pin->serial,
        ];
    }
}
