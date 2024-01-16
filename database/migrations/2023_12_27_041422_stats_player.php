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
        Schema::create('stats_player', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('strength');
            $table->integer('destrexity');
            $table->integer('intelligence');
            $table->bigInteger('hp');
            $table->bigInteger('mp');
            $table->bigInteger('level');
            $table->bigInteger('exp');
            $table->bigInteger('money');
            $table->bigInteger('diamonds');
            $table->bigInteger('rare_ores');
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
