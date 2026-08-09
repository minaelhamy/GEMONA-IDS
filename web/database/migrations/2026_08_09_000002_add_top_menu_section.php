<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('menu_sections')->updateOrInsert(
            ['id' => 3],
            [
                'name' => 'Top Menu',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('pages')->where('menu_section_id', 3)->update(['menu_section_id' => 1]);
        DB::table('menu_sections')->where('id', 3)->delete();
    }
};
