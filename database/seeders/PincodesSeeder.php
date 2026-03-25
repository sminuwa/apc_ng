<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PincodesSeeder extends Seeder
{
    /**
     * Delegate counts per state. Each state gets codes {PREFIX}-001 through {PREFIX}-{count}.
     *
     * @var array<int, array{0: string, 1: string, 2: int}>
     */
    private const STATES = [
        ['ABIA', 'ABI', 180],
        ['ADAMAWA', 'ADM', 253],
        ['AKWA IBOM', 'AKW', 326],
        ['ANAMBRA', 'ANA', 203],
        ['BAUCHI', 'BAU', 221],
        ['BAYELSA', 'BYS', 185],
        ['BENUE', 'BEN', 265],
        ['BORNO', 'BOR', 271],
        ['CROSS RIVERS', 'CRO', 254],
        ['DELTA', 'DEL', 287],
        ['EBONYI', 'EBO', 251],
        ['EDO', 'EDO', 201],
        ['EKITI', 'EKI', 204],
        ['ENUGU', 'ENU', 218],
        ['GOMBE', 'GOM', 143],
        ['IMO', 'IMO', 264],
        ['JIGAWA', 'JIG', 281],
        ['KADUNA', 'KAD', 267],
        ['KANO', 'KAN', 306],
        ['KATSINA', 'KAT', 393],
        ['KEBBI', 'KEB', 226],
        ['KOGI', 'KOG', 228],
        ['KWARA', 'KWA', 197],
        ['LAGOS', 'LAG', 278],
        ['NASARAWA', 'NAS', 203],
        ['NIGER', 'NIG', 263],
        ['OGUN', 'OGU', 271],
        ['ONDO', 'OND', 256],
        ['OSUN', 'OSU', 256],
        ['OYO', 'OYO', 261],
        ['PLATEAU', 'PLT', 213],
        ['RIVERS', 'RIV', 222],
        ['SOKOTO', 'SOK', 223],
        ['TARABA', 'TAR', 195],
        ['YOBE', 'YOB', 192],
        ['ZAMFARA', 'ZAM', 165],
        ['FEDERAL CAPITAL TERRITORY', 'FCT', 127],
    ];

    public function run(): void
    {
        $prefixes = array_map(fn (array $row) => $row[1], self::STATES);
        if (count($prefixes) !== count(array_unique($prefixes))) {
            throw new RuntimeException('Duplicate state_code prefix in PincodesSeeder::STATES.');
        }

        $now = now();

        $rows = [];
        foreach (self::STATES as [$stateName, $prefix, $count]) {
            for ($i = 1; $i <= $count; $i++) {
                $rows[] = [
                    'code' => sprintf('%s-%03d', $prefix, $i),
                    'state_code' => $prefix,
                    'state_name' => $stateName,
                    'serial' => $i,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('pincodes')->insert($chunk);
        }
    }
}
