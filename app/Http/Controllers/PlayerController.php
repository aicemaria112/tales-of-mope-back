<?php

namespace App\Http\Controllers;

use App\Events\SendSocketInfo;
use App\Models\Item;
use App\Models\PlayerModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class PlayerController extends Controller
{
    public function getPlayer(Request $request)
    {
        $user = $request->user()->id;
        // dd($user);
        $player = Cache::get('player.' . $user);

        if (empty($player)) {
            $player = new PlayerModel($user);
            Cache::put('player.' . $user, serialize($player));
            return response()->json($player);
        } else {

            return response()->json(unserialize($player));
        }
    }

    public function createPlayer(Request $request)
    {
        $user = $request->user()->id;
        $player = new PlayerModel($user);
        // dd($user);
        if ($player->player) {
            return response()->json(["message" => "You have already created a player"], 406);
        } else {

            $player_name = $request->input("name");
            $avatar = $request->input("avatar");
            $voice = $request->input('voice');
            $gender = $request->input('gender');


            if (DB::table('player_data')->where('nombre', '=', $player_name)->exists()) {
                return response()->json(["message" => "You have already created a player"], 406);
            }


            $class_id_main = $request->id_class_main;
            $class_id_sec = $request->id_seccond_class;

            $stats_mc = DB::table("class_base")->where('id', $class_id_main)->select()->first();
            $stats_sc = DB::table("class_base")->where('id', $class_id_sec)->select()->first();

            if (!($stats_mc and $stats_sc)) {
                return response()->json(["message" => "Class not exists :("], 406);
            }

            DB::beginTransaction();
            try {
                //code...

                DB::table('stats_player')->insert([
                    "id" => $user
                    ,
                    "strength" => $stats_mc->base_str + $stats_sc->base_str + 0
                    ,
                    "destrexity" => $stats_mc->base_dest + $stats_sc->base_dest + 0
                    ,
                    "intelligence" => $stats_mc->base_int + $stats_sc->base_int + 0
                    ,
                    "hp" => 100 + 50 * ($stats_mc->base_str + $stats_sc->base_str)
                    ,
                    "mp" => 100 + 30 * ($stats_mc->base_int + $stats_sc->base_int)
                    ,
                    "level" => 1
                    ,
                    "exp" => 0
                    ,
                    "money" => 1000
                    ,
                    "diamonds" => 0
                    ,
                    "rare_ores" => 0
                ]);

                DB::table('player_class')->insert([
                    'id_player' => $user,
                    'id_class' => $class_id_main,
                    'type' => 1
                ]);
                DB::table('player_class')->insert([
                    'id_player' => $user,
                    'id_class' => $class_id_sec,
                    'type' => 2
                ]);

                DB::table('player_data')->insert([
                    "id" => $user
                    ,
                    "nombre" => $player_name
                    ,
                    "id_stats" => $user
                    ,
                    "id_class_main" => $class_id_main
                    ,
                    "id_seccond_class" => $class_id_sec
                    ,
                    "avatar" => $avatar
                    ,
                    "voice" => $voice
                    ,
                    "gender" => $gender
                    ,
                    "user_id" => $user
                ]);

                DB::table("player_location")->insert([
                    "id_player" => $user,
                    "position" => json_encode([
                        "22.40694",
                        "-79.96472"
                    ]),
                    'activity' => 'idle'
                ]);

                DB::commit();
                $player = new PlayerModel($user);
                $player->update_player();

            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(["message" => $th->getMessage()], 406);

            }
            return response()->json(["message" => "Created :)"], 201);

        }
    }
    public function deletePlayer(Request $request)
    {
        $user = $request->user()->id;
        DB::table('stats_player')->where('id', $user)->delete();
        DB::table('player_data')->where('id', $user)->delete();
        DB::table('player_class')->where('id_player', $user)->delete();
        $player = new PlayerModel($user);
        $player->delete_player();
        Cache::flush();
        return response()->json(["message" => "Deleted :)"], 200);

    }

    public function getClassesMain(Request $request)
    {

        $cache = Cache::get('classes_m');

        if (!$cache) {
            $query = DB::table('class_base')->where('type', 1)->selectRaw("id,name," .
                "'mhuman.webp' as imageM," .
                "'fhuman.avif' as imageF," .
                "base_str as strength," .
                "base_dest as agility," .
                "base_int as intelligence," .
                "description as description")->get();

            Cache::put("classes_m", $query, 543);
            return $query;
        }
        return $cache;
    }
    public function getClassesSecondary(Request $request)
    {
        $cache = Cache::get('classes_sec');

        if (!$cache) {
            $query = DB::table('class_base')->where('type', 2)->selectRaw("id,name," .
                "base_str as strength," .
                "base_dest as agility," .
                "base_int as intelligence," .
                "description as description")->get();

            Cache::put("classes_sec", $query, 543);
            return $query;
        }
        return $cache;
    }



    public function getMyLocation(Request $request)
    {
        $usr = $request->user()->id;
        $pos = Cache::get("position_player." . $usr);
        if ($pos) {
            if (is_string($pos->position))
                $pos->position = json_decode($pos->position);
            return response()->json($pos);
        } else {
            $position = DB::table('player_location')->where('id_player', $usr)->select()->first();
            if ($position) {
                $position->position = json_decode($position->position);
            }
            Cache::put("position_player." . $usr, $position);
            return response()->json($position);
        }
    }
    public function setMyLocation(Request $request)
    {
        $usr = $request->user()->id;
        $requestp = json_encode($request->input('position'));
        $position = DB::table('player_location')->where('id_player', $usr)->select()->first();
        if ($position) {
            DB::table('player_location')->where('id_player', $usr)->update(['position' => $requestp]);
            $position->position = json_decode($requestp);
        } else {
            DB::table('player_location')->where('id_player', $usr)->insert(['id_player' => $usr, 'position' => $requestp, 'activity' => 'idle']);
        }
        Cache::forget('position_player.' . $usr);
        Cache::forget('players.' . $usr);
        Cache::put("position_player." . $usr, $position);
        return response()->json(['message' => 'position setted']);
    }

    public function getPlayerNear(Request $request)
    {
        $usr = $request->user()->id;
        $players = Cache::get("players_around.$usr");
        if (!$players) {
            Artisan::call('app:player-around', ['uuid' => $usr]);
            $players = Cache::get("players_around.$usr");
            return $players;
        } else {
            return $players;
        }
    }

    public function getItemsbag(Request $request)
    {
        $usr = $request->user()->id;

        // $itemsbag = Cache::get("itembag" . $usr);
        if (empty($itemsbag)) {
            $items_bag = DB::table("item_bag")->join('item_base', 'item_bag.id_item', '=', 'item_base.id')->where("id_player", $usr)->whereNotIn('equipable', ['potion', 'teleport'])->limit(100)->get();
            $potions = DB::table("item_bag")->join('item_base', 'item_bag.id_item', '=', 'item_base.id')->where("id_player", $usr)->where('equipable', '=', 'potion')->limit(2)->orderBy('item_base.id')->get();
            $teleports = DB::table("item_bag")->join('item_base', 'item_bag.id_item', '=', 'item_base.id')->where("id_player", $usr)->where('equipable', '=', 'teleport')->limit(1)->orderBy('item_base.id')->get();
            foreach ($items_bag as $value) {
                $value->data_extra = json_decode($value->data_extra);
            }
            $data = ["items" => $items_bag, "potions" => $potions, 'teleports' => $teleports];
            // Cache::put("itembag" . $usr, $items_bag);
            return $data;
        } else {
            return $itemsbag;
        }
    }
    public function equipItem(Request $request)
    {
        $usr = $request->user()->id;
        $id_item = $request->id_item;

        $item = new Item();
        $item = $item->item($id_item);
        // dd(json_encode($item));
        if (DB::table("equipament_set")->where("set_item", $item->equipable)->where('id_player', $usr)->exists()) {
            DB::table("equipament_set")->where("set_item", $item->equipable)->update(['data_item' => json_encode($item)]);
        } else {
            DB::table("equipament_set")->insert(['id_player' => $usr, 'set_item' => $item->equipable, 'data_item' => json_encode($item)]);
        }
        $ps = new PlayerModel($usr);
        $ps->update_player();
        return response()->json(["message" => "setted"], 200);
    }

    public function getMySet(Request $request)
    {
        $usr = $request->user()->id;

        $set = Cache::get('set_player.' . $usr);
        if (!$set) {
            $sets = DB::table('equipament_set')->where('id_player', $usr)->select()->get();

            $object_set = new \stdClass();
            $object_set->head_item = $sets->where('set_item', 'head_item')->first();
            // if($object_set->head_item){
            //     $object_set->head_item->data_item = json_decode($object_set->head_item->data_item);
            // }
            $object_set->wl_item = $sets->where('set_item', 'wl_item')->first();
            $object_set->wr_item = $sets->where('set_item', 'wr_item')->first();
            $object_set->legs_item = $sets->where('set_item', 'legs_item')->first();
            $object_set->arm_item = $sets->where('set_item', 'arm_item')->first();
            $object_set->foot_item = $sets->where('set_item', 'foot_item')->first();
            $object_set->chest_item = $sets->where('set_item', 'chest_item')->first();
            $object_set->ring[] = $sets->where('set_item', 'ring_1')->first();
            $object_set->ring[] = $sets->where('set_item', 'ring_2')->first();
            $object_set->ring[] = $sets->where('set_item', 'ring_3')->first();
            $object_set->ring[] = $sets->where('set_item', 'ring_4')->first();
            $object_set->ring[] = $sets->where('set_item', 'ring_5')->first();

            $stats_rev = self::calcstats($object_set, $usr);
            $set = Cache::put('set_player.' . $usr, $stats_rev);

            return $stats_rev;
        } else {
            return $set;
        }
    }
    public static function calcstats($object_set, $usr)
    {
        $stats_item = new \stdClass();
        $stats_item->str = 0;
        $stats_item->dex = 0;
        $stats_item->int = 0;
        $stats_item->atk = 0;
        $stats_item->def = 0;
        $stats_item->mpw = 0;
        $stats_item->mdf = 0;
        $stats_item->vel = 0;
        $stats_item->eva = 0;
        $stats_item->res = 0;
        $stats_item->crit_rate = 0;
        $stats_item->crit_damage = 0;
        $stats_item->counter_spell = 0;
        $stats_item->counter_atk = 0;
        $stats_item->heal_pwr = 0;

        foreach ($object_set as $key => $value) {
            if ($value != null) {
                if (is_object($value)) {
                    $value->data_item = json_decode($value->data_item);
                    $stats_item->str += $value->data_item->base_str;
                    $stats_item->dex += $value->data_item->base_dext;
                    $stats_item->int += $value->data_item->base_int;
                    $prt = $value->data_item->data_extra;
                    if (property_exists($prt, 'atk')) {
                        $stats_item->atk += $prt->atk;
                    }
                    if (property_exists($prt, 'def')) {
                        $stats_item->def += $prt->def;
                    }
                    if (property_exists($prt, 'mpw')) {
                        $stats_item->mpw += $prt->mpw;
                    }
                    if (property_exists($prt, 'mdf')) {
                        $stats_item->mdf += $prt->mdf;
                    }
                    if (property_exists($prt, 'vel')) {
                        $stats_item->vel += $prt->vel;
                    }
                    if (property_exists($prt, 'eva')) {
                        $stats_item->eva += $prt->eva;
                    }
                    if (property_exists($prt, 'res')) {
                        $stats_item->res += $prt->res;
                    }
                    if (property_exists($prt, 'crit_rate')) {
                        $stats_item->crit_rate += $prt->crit_rate;
                    }
                    if (property_exists($prt, 'crit_damage')) {
                        $stats_item->crit_damage += $prt->crit_damage;
                    }
                    if (property_exists($prt, 'counter_spell')) {
                        $stats_item->counter_spell += $prt->counter_spell;
                    }
                    if (property_exists($prt, 'counter_atk')) {
                        $stats_item->counter_atk += $prt->counter_atk;
                    }
                    if (property_exists($prt, 'heal_pwr')) {
                        $stats_item->heal_pwr += $prt->heal_pwr;
                    }
                } else {
                    foreach ($value as $rings) {
                        if ($rings) {
                            $rings->data_item = json_decode($rings->data_item);
                            $stats_item->str += $rings->data_item->base_str;
                            $stats_item->dex += $rings->data_item->base_dext;
                            $stats_item->int += $rings->data_item->base_int;

                            if (property_exists($rings, 'atk')) {
                                $stats_item->atk += $rings->atk;
                            }
                            if (property_exists($rings, 'def')) {
                                $stats_item->def += $rings->def;
                            }
                            if (property_exists($rings, 'mpw')) {
                                $stats_item->mpw += $rings->mpw;
                            }
                            if (property_exists($rings, 'mdf')) {
                                $stats_item->mdf += $rings->mdf;
                            }
                            if (property_exists($rings, 'vel')) {
                                $stats_item->vel += $rings->vel;
                            }
                            if (property_exists($rings, 'eva')) {
                                $stats_item->eva += $rings->eva;
                            }
                            if (property_exists($rings, 'res')) {
                                $stats_item->res += $rings->res;
                            }
                            if (property_exists($rings, 'crit_rate')) {
                                $stats_item->crit_rate += $rings->crit_rate;
                            }
                            if (property_exists($rings, 'crit_damage')) {
                                $stats_item->crit_damage += $rings->crit_damage;
                            }
                            if (property_exists($rings, 'counter_spell')) {
                                $stats_item->counter_spell += $rings->counter_spell;
                            }
                            if (property_exists($rings, 'counter_atk')) {
                                $stats_item->counter_atk += $rings->counter_atk;
                            }
                            if (property_exists($rings, 'heal_pwr')) {
                                $stats_item->heal_pwr += $rings->heal_pwr;
                            }
                        }
                    }
                }
            }
        }
        $object_set->stats_calc = $stats_item;
        Cache::forget('stats_extra.' . $usr);
        Cache::put('stats_extra.' . $usr, $stats_item);
        return $object_set;
    }

    public function sellItem(Request $request)
    {
        $usr = $request->user()->id;
        $item_id = $request->input('id_item');
        $quantity = $request->input('quantity') ?? 1;

        $items_bag = DB::table("item_bag")->join('item_base', 'item_bag.id_item', '=', 'item_base.id')->where("id_player", $usr)
            ->where('item_bag.id_item', $item_id)
            ->limit(100)->first();

        if (!$items_bag) {
            return response()->json(["message" => "We cant find the item"], 404);
        } else {
            $monryp = DB::table('stats_player')->where('id', $usr)->select('money')->first();
            if ($quantity > $items_bag->quantity) {
                return response()->json(["message" => "Out of order"], 406);

            } else {
                if ($quantity == $items_bag->quantity) {
                    DB::table("item_bag")->where("id_player", $usr)
                        ->where('id_item', $item_id)->delete();
                } else {
                    DB::table("item_bag")->where("id_player", $usr)
                        ->where('id_item', $item_id)->update(['quantity' => $items_bag->quantity - $quantity]);
                    DB::table('stats_player')->where('id', $usr)->update(['money' => $monryp->money + $items_bag->sell_price * $quantity]);
                }
                $ps = new PlayerModel($usr);
                $ps->update_player();
                return response()->json(["message" => "Item selled"], 200);
            }
        }
    }
    public function dropItem(Request $request)
    {
        $usr = $request->user()->id;
        $item_id = $request->input('id_item');


        $items_bag = DB::table("item_bag")->join('item_base', 'item_bag.id_item', '=', 'item_base.id')->where("id_player", $usr)
            ->where('item_bag.id_item', $item_id)
            ->limit(100)->first();

        if (!$items_bag) {
            return response()->json(["message" => "We cant find the item"], 404);
        } else {
            DB::table("item_bag")->where("id_player", $usr)
                ->where('id_item', $item_id)->delete();
            $ps = new PlayerModel($usr);
            $ps->update_player();
            return response()->json(["message" => "Item droped"], 200);
        }
    }

    public function consumeitem(Request $request)
    {
        $usr = $request->user()->id;
        $item_id = $request->input("id_item");

        $items_bag = DB::table("item_bag")->join('item_base', 'item_bag.id_item', '=', 'item_base.id')->where("id_player", $usr)
            ->where('item_bag.id_item', $item_id)
            ->limit(1)->first();
        $player = Cache::get('player.' . $usr);
        $player = unserialize($player);

        if (!$items_bag) {
            return response()->json(['message' => "We cant find the item"], 404);
        } else {
            $heal = json_decode($items_bag->data_extra);
            switch ($items_bag->id_item) {
                case '99':
                    $player->stats->current_hp = min($player->stats->current_hp + $heal->heal, $player->stats->hp);
                    $player->update_player();
                    break;
                case '100':
                    $player->stats->current_mp = min($player->stats->current_mp + $heal->mana, $player->stats->mp);
                    $player->update_player();
                    break;
                case '101':
                    $requestp = json_encode($request->input('position'));
                    DB::table('player_location')->where('id_player', $usr)->update(['position' => $requestp]);
                    $position = DB::table('player_location')->where('id_player', $usr)->select()->first();
                    Cache::forget('position_player.' . $usr);
                    Cache::put("position_player." . $usr, $position);
                    break;
                default:
                    return response()->json(['message' => "this not a consumible item"], 406);
            }
            if ($items_bag->quantity == 1) {
                DB::table("item_bag")->where("id_player", $usr)
                    ->where('id_item', $item_id)
                    ->delete();
            } else {
                DB::table("item_bag")->where("id_player", $usr)
                    ->where('id_item', $item_id)
                    ->update(['quantity' => $items_bag->quantity - 1]);
            }
        }
        $object = new \stdClass();
        $object->channel = "player." . $usr;
        $object->event = "hited";
        $object->message = "{\"message\":\"testing hitted player\"}";
        event(new SendSocketInfo($object));
        
        return ["consumed_item" => $items_bag, "player_stats" => $player->stats];
    }

    public function hitme(Request $request)
    {
        $usr = $request->user()->id;
        $player = Cache::get('player.' . $usr);
        $player = unserialize($player);
        $player->stats->current_hp = $player->stats->current_hp - 100;
        $player->stats->current_mp = $player->stats->current_hp - 100;
        $player->update_player();
        $object = new \stdClass();
        $object->channel = "player." . $usr;
        $object->event = "hited";
        $object->message = "{\"message\":\"testing hitted player\"}";
        event(new SendSocketInfo($object));
        return ["message" => "you are crazily broken"];
    }
}
