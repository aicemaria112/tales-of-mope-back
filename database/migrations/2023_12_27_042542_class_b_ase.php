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
        Schema::create('class_base', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('base_str');
            $table->integer('base_dest');
            $table->integer('base_int');
            $table->integer('type');
            $table->string('description');
            $table->string('image')->nullable(true);
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
