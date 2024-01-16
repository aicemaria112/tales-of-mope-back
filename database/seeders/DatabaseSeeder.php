<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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

DB::select('TRUNCATE item_base;');
DB::select("INSERT INTO item_base (name, req_str, req_dext, req_int, base_str, base_dext, base_int, max_stock, item_quest, exclusive, probability,equipable,data_extra)
VALUES 
    ('Sword of Strength', 10, 0, 0, 15, 0, 0, 50, true, false, 0.05,true,'{}'),
    ('Bow of Precision', 0, 10, 0, 0, 20, 0, 40, true, false, 0.08,true,'{}'),
    ('Robe of Wisdom', 0, 0, 15, 0, 0, 25, 30, false, false, 0.03,true,'{}'),
    -- Add 47 more items with similar structure
    ('Shield of Protection', 8, 4, 2, 12, 6, 3, 20, false, false, 0.07,true,'{}'),
    ('Amulet of Luck', 0, 0, 0, 0, 0, 0, 10, false, true, 0.02,true,'{}'),
    ('Potion of Healing', 0, 0, 0, 0, 0, 0, 100, false, false, 0.1,false,'{}');");


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
