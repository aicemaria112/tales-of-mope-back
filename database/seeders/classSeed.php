<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class classSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      DB::connection("pgsql")->select("TRUNCATE class_base");
     DB::connection("pgsql")->select("INSERT INTO class_base (name, base_str, base_dest, base_int,type, description) 
VALUES 
    ('Warrior', 10, 5, 2, 2, 'A strong and resilient fighter excelling in melee combat.'),
    ('Archer', 5, 8, 3,2, 'An expert archer with exceptional agility and accuracy.'),
    ('Mage', 2, 4, 10, 2,'A powerful spellcaster mastering mystical arts and magic.'),
    ('Rogue', 4, 7, 5, 2,'A cunning thief specializing in stealth and theft.'),
    ('Cleric', 6, 3, 8, 2,'A devoted healer with curative abilities and spiritual knowledge.'),
    ('Human', 8, 6, 5,1, 'Versatile and adaptable, humans excel in various fields.'),
    ('Elf', 3, 8, 7,1, 'Elegant and agile, elves have a natural affinity for magic.'),
    ('Dwarf', 9, 3, 4,1, 'Sturdy and strong, dwarves are skilled craftsmen and warriors.'),
    ('Halfling', 4, 9, 6,1, 'Small and nimble, halflings are known for their luck and stealth.'),
    ('Orc', 10, 5, 2, 1,'Brutal and powerful, orcs thrive in combat and warfare.')
    ;");
    }
}
