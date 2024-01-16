<?php

namespace App\Console\Commands;

use App\Events\SendSocketInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use stdClass;
use Illuminate\Support\Facades\Cache;

class SendNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $object = new stdClass();
        // /**
        //  * channel
        //  *  event
        //  *  message
        //  */
        $object->channel = "default";
        $object->event = "event-default";
        $object->message = "{\"message\":\"hello World\"}";
        // // Store data in the cache
        // Cache::put('key', 'value', 60);
        // // Retrieve data from the cache
        // $value = Cache::get('key');
        event(new SendSocketInfo($object));
        DB::select('alter table item_base add column image_url text null');
    }
}
