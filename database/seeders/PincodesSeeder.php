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
        ['ABIA', 'ABI', 165],
        ['ADAMAWA', 'ADM', 243],
        ['AKWA IBOM', 'AKW', 300],
        ['ANAMBRA', 'ANA', 210],
        ['BAUCHI', 'BAU', 196],
        ['BAYELSA', 'BYS', 169],
        ['BENUE', 'BEN', 258],
        ['BORNO', 'BOR', 269],
        ['CROSS RIVER', 'CRO', 250],
        ['DELTA', 'DEL', 280],
        ['EBONYI', 'EBO', 199],
        ['EDO', 'EDO', 196],
        ['EKITI', 'EKI', 210],
        ['ENUGU', 'ENU', 209],
        ['GOMBE', 'GOM', 139],
        ['IMO', 'IMO', 262],
        ['JIGAWA', 'JIG', 273],
        ['KADUNA', 'KAD', 265],
        ['KANO', 'KAN', 408],
        ['KATSINA', 'KAT', 340],
        ['KEBBI', 'KEB', 217],
        ['KOGI', 'KOG', 182],
        ['KWARA', 'KWA', 187],
        ['LAGOS', 'LAG', 283],
        ['NASARAWA', 'NAS', 196],
        ['NIGER', 'NIG', 257],
        ['OGUN', 'OGU', 266],
        ['ONDO', 'OND', 249],
        ['OSUN', 'OSU', 254],
        ['OYO', 'OYO', 250],
        ['PLATEAU', 'PLT', 208],
        ['RIVERS', 'RIV', 215],
        ['SOKOTO', 'SOK', 214],
        ['TARABA', 'TAR', 192],
        ['YOBE', 'YOB', 190],
        ['ZAMFARA', 'ZAM', 157],
        ['FEDERAL CAPITAL TERRITORY', 'FCT', 71],
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
