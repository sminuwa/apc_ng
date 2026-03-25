<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ConventionCategoryPincodesSeeder extends Seeder
{
    /**
     * Category totals (convention / non-state). Prefixes are unique vs Nigerian state codes.
     *
     * @var array<int, array{0: string, 1: string, 2: int}>
     */
    private const CATEGORIES = [
        ['VVIP', 'VVP', 210],
        ['VIP', 'VIP', 371],
        ['International Observers', 'OBS', 20],
        ['Domestic observers', 'DOM', 20],
        ['INEC Officials', 'INE', 30],
        ['Ushers', 'USH', 360],
        ['Medical', 'MED', 130],
        ['Press', 'PRS', 210],
        ['Caterers', 'CAT', 60],
        ['Election support', 'ESU', 60],
        ['Cleaning', 'CLN', 60],
        ['Venue planners', 'VNP', 110],
        ['Rapparteurs/ documentation', 'RPD', 25],
        ['Entertainment', 'ENT', 110],
        ['Emergency Response', 'EMR', 60],
        ['Secretariat', 'SCR', 110],
        ['Security', 'SEC', 210],
        ['Technical', 'TEC', 60],
        ['Women and Youth', 'WMY', 210],
        ['PWD', 'PWD', 30],
        ['Support Groups', 'SPG', 210],
        ['NCC Officials', 'NCC', 1010],
    ];

    public function run(): void
    {
        if (DB::table('pincodes')->where('state_code', 'VVP')->exists()) {
            return;
        }

        $prefixes = array_map(fn (array $row) => $row[1], self::CATEGORIES);
        if (count($prefixes) !== count(array_unique($prefixes))) {
            throw new RuntimeException('Duplicate state_code prefix in ConventionCategoryPincodesSeeder.');
        }

        $now = now();
        $rows = [];
        foreach (self::CATEGORIES as [$name, $prefix, $count]) {
            $pad = max(3, strlen((string) $count));
            for ($i = 1; $i <= $count; $i++) {
                $rows[] = [
                    'code' => sprintf('%s-%0'.$pad.'d', $prefix, $i),
                    'state_code' => $prefix,
                    'state_name' => $name,
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
