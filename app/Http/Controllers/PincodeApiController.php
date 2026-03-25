<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PincodeApiController extends Controller
{
    /** Max pincodes per batch request (offline queue flush). */
    private const PUSH_BATCH_MAX = 200;

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
     * Mark one pincode as used. Body: pincode.
     *
     * status: 0 on success or already used; 2 if not found.
     */
    public function push(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pincode' => ['required', 'string', 'max:32'],
        ]);

        $code = $this->normalizePincode($validated['pincode']);
        $result = $this->pushSingle($code);

        $http = $result['status'] === Pincode::STATUS_INVALID ? 404 : 200;

        return response()->json($result, $http);
    }

    /**
     * Mark many pincodes as used (e.g. cached scans flushed when online).
     * Body: { "pincodes": ["JIG-001", "VIP-002", ...] }
     *
     * HTTP 200 always; each item has status 0 / 1 / 2. Duplicates in the array are applied once (first wins).
     */
    public function pushBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pincodes' => ['required', 'array', 'min:1', 'max:'.self::PUSH_BATCH_MAX],
            'pincodes.*' => ['required', 'string', 'max:32'],
        ]);

        set_time_limit(180);

        $seen = [];
        $results = [];

        foreach ($validated['pincodes'] as $raw) {
            $code = $this->normalizePincode($raw);
            if (isset($seen[$code])) {
                continue;
            }
            $seen[$code] = true;
            $results[] = $this->pushSingle($code);
        }

        return response()->json([
            'count' => count($results),
            'results' => $results,
        ]);
    }

    /**
     * Apply push logic for one normalized code (shared by push and pushBatch).
     *
     * @return array<string, mixed>
     */
    private function pushSingle(string $code): array
    {
        $pin = Pincode::query()->where('code', $code)->first();

        if ($pin === null) {
            return [
                'pincode' => $code,
                'status' => Pincode::STATUS_INVALID,
                'message' => 'Pincode not found.',
            ];
        }

        if ((int) $pin->status === Pincode::STATUS_USED) {
            return array_merge(
                [
                    'message' => 'Pincode was already used.',
                    'pincode' => $code,
                ],
                $this->pincodePayload($pin)
            );
        }

        $pin->status = Pincode::STATUS_USED;
        $pin->save();

        return array_merge(
            [
                'message' => 'Pincode marked as used.',
                'pincode' => $code,
            ],
            $this->pincodePayload($pin->fresh())
        );
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
