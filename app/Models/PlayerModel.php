<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Integer;

class PlayerModel
{
    use HasFactory;
    public $uuid = "";
    public $player = null;
    public $items = [];
    public $stats = null;
    public $class_main = null;
    public $class_second = null;
    public $skills = [];
    public $cached = false;

public function __construct(int $uuid) {
    $player_old = Cache::get('player.'.$this->uuid);
    if ($player_old) {
        $player_old = unserialize($player_old);  
    }

    $this->uuid = $uuid;
    if(!$this->cached){
        $this->player = DB::table('player_data')->where('user_id',$uuid)->select()->first();
        if($this->player){
            $this->stats = DB::table('stats_player')->where('id',$this->player->id_stats)->select()->first();
            if($this->stats){
                !$player_old ? ($this->stats->current_hp = $this->stats->hp) : $player_old->stats->current_hp;
                !$player_old ? ($this->stats->current_mp  = $this->stats->mp) : $player_old->stats->current_mp;
                !$player_old ? ($this->stats->next_level_exp = $this->stats->level * (521)+ intval($this->stats->level*0.75+200)) : $player_old->stats->next_level_exp;
            }
            $this->items = DB::table('item_bag')->join('item_base','item_bag.id_item','=','item_bag.id')->where('id_player',$this->player->id)->select()->get();

            $this->class_main = DB::table('player_class')->join('class_base','player_class.id_class','=','class_base.id')->where('id_player',$this->player->id)->where('player_class.type',1)->select()->first();

            $this->class_second = DB::table('player_class')->join('class_base','player_class.id_class','=','class_base.id')->where('player_class.type',2)->where('id_player',$this->player->id)->select()->first();

            $this->skills = DB::table('skill_earned')->join('skill_base','skill_earned.id_skill','=','skill_base.id')->where('id_player',$this->player->id)->select()->get();           
        }
        $this->cached = true;
        Cache::put('player.'.$this->uuid, serialize($this));
    }  
}

public function update_player(){
    Cache::forget('player.'.$this->uuid);
    Cache::put('player.'.$this->uuid, serialize($this));
}
public function delete_player(){
    Cache::forget('player.'.($this->uuid ?$this->uuid:4));

}
}
