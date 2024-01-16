<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class itemSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::select('TRUNCATE item_base;');
        DB::select("INSERT INTO item_base (name, req_str, req_dext, req_int, base_str, base_dext, base_int, max_stock, item_quest, exclusive, probability)
        VALUES 
            ('Sword of Strength', 10, 0, 0, 15, 0, 0, 50, true, false, 0.05),
            ('Bow of Precision', 0, 10, 0, 0, 20, 0, 40, true, false, 0.08),
            ('Robe of Wisdom', 0, 0, 15, 0, 0, 25, 30, false, false, 0.03),
            -- Add 47 more items with similar structure
            ('Shield of Protection', 8, 4, 2, 12, 6, 3, 20, false, false, 0.07),
            ('Amulet of Luck', 0, 0, 0, 0, 0, 0, 10, false, true, 0.02),
            ('Potion of Healing', 0, 0, 0, 0, 0, 0, 100, false, false, 0.1);");
    }
}
