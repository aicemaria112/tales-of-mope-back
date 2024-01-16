<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_base', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('req_str');
            $table->integer('req_dext');
            $table->integer('req_int');
            $table->integer('base_str');
            $table->integer('base_dext');
            $table->integer('base_int');
            $table->integer('max_stock');
            $table->boolean('item_quest');
            $table->boolean('exclusive');
            $table->float('probability');
            $table->text('equipable')->nullable();            
            $table->json('data_extra')->default('{}');
            $table->text('image_url')->nullable();            
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
