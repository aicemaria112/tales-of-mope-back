<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class skillBAse extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::select("INSERT INTO skill_base (name, id_class, mana_requested, coldown, damage_base, cast_time, element,lvl_req,parent_skill)
       VALUES 
           ('Slash', 1, 10, 5, 20, 2, 'Physical',10,0),
           ('Shield Bash', 1, 15, 8, 15, 3, 'Physical',20,0),
           ('Whirlwind', 1, 20, 12, 30, 4, 'Physical',30,0),
           ('Arrow Shot', 2, 8, 3, 18, 1, 'Physical',10,0),
          ('Volley', 2, 12, 6, 25, 2, 'Physical',20,0),
    ('Snipe', 2, 15, 8, 40, 3, 'Physical',30,0),
    ('Fireball', 3, 25, 10, 35, 4, 'Fire',10,0),
    ('Frost Nova', 3, 18, 7, 28, 3, 'Ice',20,0),
    ('Arcane Missile', 3, 30, 12, 45, 5, 'Arcane',30,0),
    ('Backstab', 4, 12, 6, 25, 2, 'Physical',10,0),
    ('Stealth', 4, 8, 15, 0, 1, 'Shadow',20,0),
    ('Poison Strike', 4, 15, 8, 30, 3, 'Poison',30,0),
    ('Heal', 5, 20, 10, 0, 3, 'Holy',10,0),
    ('Divine Smite', 5, 25, 12, 40, 4, 'Holy',20,0),
    ('Blessing', 5, 15, 8, 0, 2, 'Holy',30,0)
           ;");
    }
}
