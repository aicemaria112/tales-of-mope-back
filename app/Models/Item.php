<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Item extends Model
{
    use HasFactory;
    protected $table = 'item_base';
    protected $fillable = [
        "id",
        "name",
        "req_str",
        "req_dext",
        "req_int",
        "base_str",
        "base_dext",
        "base_int",
        "max_stock",
        "item_quest",
        "exclusive",
        "probability",
        "equipable",
        'image_url',
        "data_extra",
        "sell_price",
    ];

    public function item(string $id){
        $item_cached = Cache::get('item.'.$id);
        if($item_cached){
            return  $item_cached;
        }
        $item =  $this->where('id', $id)->first();
        if($item){
            $item->data_extra = json_decode($item->data_extra);
        }
        Cache::put('item.'.$id,$item);
        return $item;
    }
    public function items(){
        $item_cached = Cache::get('item.*');
        // dd($item_cached);

        if(empty($item_cached)){
            $todos = $this->select()->get()->all();
            foreach ($todos as  $item) {
              $item->data_extra = json_decode($item->data_extra);   
            }
            Cache::put('item.*',$todos);
            return $todos;
        }else{
            $ids = Utils::toArray($item_cached,'id');
            $todos_excpt =  $this->whereNotIn('id',$ids)->select()->get()->all();
            foreach ($todos_excpt as  $item) {
                $item->data_extra = json_decode($item->data_extra);     
                Cache::put('item.'.$item->id,$item);
                array_push($item_cached,$item);
            }
            
            // dd($item_cached);
            Cache::put('item.*',$item_cached);
        }
        $item_cached = Cache::get('item.*');
        return $item_cached;
    }

    public function save(array $options = []){
        Cache::forget('item.'.$this->id);
        Cache::forget('item.*');
        $this->data_extra = json_encode($this->data_extra);
        return parent::save($options);
    }

    public function update(array $attr=[],array $options = []){
        Cache::forget('item.'.$this->id);
        Cache::forget('item.*');
        return parent::update($attr,$options);
    }

    public function delete(){
        Cache::forget('item.'.$this->id);
        Cache::forget('item.*');
        return parent::delete();
    }

    public function clearitems(){
       Cache::forget('item.*');
       $items =  $this->select()->get();
       foreach ($items as $item) {
        $item->delete();
    }
    return ['message'=>"what have we done :(",'status'=>200];
    }
}
