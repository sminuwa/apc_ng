<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ConventionCategoryPincodesSeeder extends Seeder
{
    /**
     * Category totals from convention planning sheet.
     * Prefixes are unique and do not overlap Nigerian state codes.
     * Two "Caterers" lines use distinct prefixes (CAT / CEB).
     *
     * @var array<int, array{0: string, 1: string, 2: int}>
     */
    private const CATEGORIES = [
        ['Statebox', 'STB', 200],
        ['VIP', 'VIP', 365],
        ['Convention Officials', 'COF', 1000],
        ['Ushers', 'USH', 300],
        ['Caterers', 'CAT', 25],
        ['Medical', 'MED', 120],
        ['Press', 'PRS', 200],
        ['Caterers (additional)', 'CEB', 50],
        ['Election support', 'ESU', 10],
        ['Cleaning', 'CLN', 50],
        ['Security', 'SEC', 100],
        ['Venue planners', 'VNP', 100],
        ['Secretariat', 'SCR', 100],
    ];

    public function run(): void
    {
        if (DB::table('pincodes')->where('state_code', 'STB')->exists()) {
            return;
        }

        $prefixes = array_map(fn (array $row) => $row[1], self::CATEGORIES);
        if (count($prefixes) !== count(array_unique($prefixes))) {
            throw new RuntimeException('Duplicate state_code prefix in ConventionCategoryPincodesSeeder.');
        }

        $now = now();
        $rows = [];
        foreach (self::CATEGORIES as [$name, $prefix, $count]) {
            for ($i = 1; $i <= $count; $i++) {
                $rows[] = [
                    'code' => sprintf('%s-%03d', $prefix, $i),
                    'state_code' => $prefix,
                    'state_name' => $name,
                    'serial' => $i,
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
