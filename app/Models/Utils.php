<?php

namespace App\Models;



class Utils 
{

    public static function generateRandomString($length = 10, $chars = ""){
        
    }
    public static function toArray($coll, $column)
    {

        $array_ = [];
        foreach ($coll as $item) {
            $item = json_decode(json_encode($item, JSON_UNESCAPED_UNICODE));
            array_push($array_, get_object_vars($item)[$column]);
            # code...
        }
        return $array_;
    }
}
