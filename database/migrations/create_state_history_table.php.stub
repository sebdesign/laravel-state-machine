<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('state_history', function (Blueprint $table) {
           $table->increments('id');
           $table->string('transition');
           $table->string('from');
           $table->string('to');
           $table->integer('actor_id')->nullable();
           $table->morphs('statable');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropTable('state_history');
    }
}