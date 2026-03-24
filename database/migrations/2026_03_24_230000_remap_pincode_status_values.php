<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Flip stored values from the previous scheme (0 = valid, 1 = used) to:
     * 0 = used, 1 = valid, 2 = invalid (API only).
     */
    public function up(): void
    {
        DB::statement('UPDATE pincodes SET status = CASE status WHEN 0 THEN 1 WHEN 1 THEN 0 ELSE status END');
    }

    /**
     * Reverse the swap.
     */
    public function down(): void
    {
        DB::statement('UPDATE pincodes SET status = CASE status WHEN 0 THEN 1 WHEN 1 THEN 0 ELSE status END');
    }
};
