<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\Image;

class ItemController extends Controller
{
    public function index(){
        $items = new Item();
        //$items = Item::items("created_at","desc")->paginate(10);
        return $items->items();
    }
    public function show($id){
        $items = new Item();
        return $items->item($id);
    }
    public function overdestroy(){
        $items = new Item();
        return $items->clearitems();
    }

    public function store(Request $request){
        $items = $request->all();
        // dd($items);
        $items = new Item($items);
        return $items->save();
    }


    public function uploadimage(Request $request)
{
    // dd($request->image);
    if ($request->has('image')) {
        $file = $request->file('image');
        $filename = $file->getClientOriginalName();

        $picture = 'items/'.date('Hisu') . '' . $filename;


        Storage::disk('public')->put($picture, $file->getContent());

        $url = url(Storage::disk('public')->url($picture));
        $itm = new Item();
        $myitem = $itm->item($request->input('id_item'));
        $myitem->image_url = $url;
        $myitem->update();
        return response()->json(['message' => 'Image Uploaded Successfully']);
    }
    return response()->json(['message' => 'Select image first.']);
}

    public function pickItem(Request $request){
        $item = $request->input('id_item');

        if(DB::table('item_bag')->where('id_item',$item)->exists()){
           $qunatity = $request->input('quantity');
           $rt = DB::table('item_bag')->where('id_item',$item)->select()->first();
            DB::table('item_bag')->where('id_item',$item)->update(['quantity'=>$rt->quantity+$qunatity]);
            return response()->json(['message'=> 'item already taken'],203);
        }else{
           $item =  Item::findOrFail( $item);
           $usr = $request->user()->id;
           $qunatity = $request->input('quantity');
           DB::table('item_bag')->insert(['id_player'=>$usr,'id_item'=>$item->id,'quantity'=>$qunatity]);
           return response()->json(['message'=> 'Item setted'],200);
        }
    }
}
