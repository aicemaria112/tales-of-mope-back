<?php

namespace App\Console\Commands;

use App\Models\PlayerModel;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PlayerAround extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:player-around {uuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    function haversine($lat1, $lon1, $lat2, $lon2)
    {
        // Convierte las coordenadas de grados a radianes
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Fórmula haversine
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = 6371 * $c;  // Radio de la Tierra en kilómetros (aproximado)

        return $distance;
    }

    function puntosCercanos($posicion, $puntos, $radio)
    {
        $cercanos = [];
        $posicion->position = is_string($posicion->position) ? json_decode($posicion->position): $posicion->position;

        foreach ($puntos as $punto) {
            $punto->position = is_string($punto->position) ? json_decode($punto->position) : $punto->position;
            $lat = $punto->position[0];
            $lon = $punto->position[1];
            $distancia = $this->haversine($posicion->position[0], $posicion->position[1], $lat, $lon);

            if ($distancia <= $radio) {
                $punto_set = Cache::get('set_player.' . $punto->id_player);
                $punto->id_class_main = DB::table('class_base')->where('id',$punto->id_class_main)->select()->first()->name;
                $punto->item_set = $punto_set;
                $punto->stats = (new PlayerModel($punto->id_player))->stats;
                $cercanos[] = $punto;
            }
        }

        return $cercanos;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $param = $this->argument('uuid');
        // Ejemplo de uso
        // dd($param);
        $position = Cache::get("position_player." . $param);
        // dd($position);
        if (!$position) {
            $position = DB::table('player_location')->where('id_player', $param)->first();
        }
        if ($position) {
            $players = Cache::get("players.".$param);
            if (!$players) {
                $players = DB::table('player_location as pp')->join('player_data as pd', 'pd.id', '=', 'pp.id_player')->join('stats_player as sp', 'sp.id', '=', 'pd.id_stats')->where('pp.id_player','<>',$param)->select()->get();
            //    dd($players);
                Cache::put("players.".$param, $players);
            }
            $posicionActual = $position;
            $arregloDePuntos = $players;
            $radioBusqueda = 100;  // en kilómetros

            $puntosCercanos = $this->puntosCercanos($posicionActual, $arregloDePuntos, $radioBusqueda);
            //  dd($puntosCercanos);
            Cache::put("players_around.$param", $puntosCercanos,30);
           return $puntosCercanos;
        }else{
            return [];
        }
    }
}
