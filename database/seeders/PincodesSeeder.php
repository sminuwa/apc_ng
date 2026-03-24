<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PincodesSeeder extends Seeder
{
    /**
     * Delegate counts per state (LEGEND DELEGATES PER STATE).
     * Each state gets codes {PREFIX}-001 through {PREFIX}-{count}.
     *
     * @var array<int, array{0: string, 1: string, 2: int}>
     */
    private const STATES = [
        ['FEDERAL CAPITAL TERRITORY', 'FCT', 82],
        ['ABIA', 'ABI', 175],
        ['ADAMAWA', 'ADM', 253],
        ['AKWA IBOM', 'AKW', 327],
        ['ANAMBRA', 'ANA', 202],
        ['BAUCHI', 'BAU', 205],
        ['BAYELSA', 'BYS', 175],
        ['BENUE', 'BEN', 266],
        ['BORNO', 'BOR', 271],
        ['CROSS RIVER', 'CRO', 324],
        ['DELTA', 'DEL', 288],
        ['EBONYI', 'EBO', 204],
        ['EDO', 'EDO', 147],
        ['EKITI', 'EKI', 217],
        ['ENUGU', 'ENU', 215],
        ['GOMBE', 'GOM', 143],
        ['IMO', 'IMO', 267],
        ['JIGAWA', 'JIG', 197],
        ['KADUNA', 'KAD', 262],
        ['KANO', 'KAN', 342],
        ['KATSINA', 'KAT', 272],
        ['KEBBI', 'KEB', 275],
        ['KOGI', 'KOG', 122],
        ['KWARA', 'KWA', 190],
        ['LAGOS', 'LAG', 287],
        ['NASARAWA', 'NAS', 202],
        ['NIGER', 'NIG', 263],
        ['OGUN', 'OGU', 270],
        ['ONDO', 'OND', 254],
        ['OSUN', 'OSU', 256],
        ['OYO', 'OYO', 193],
        ['PLATEAU', 'PLT', 211],
        ['RIVERS', 'RIV', 223],
        ['SOKOTO', 'SOK', 222],
        ['TARABA', 'TAR', 198],
        ['YOBE', 'YOB', 213],
        ['ZAMFARA', 'ZAM', 167],
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
