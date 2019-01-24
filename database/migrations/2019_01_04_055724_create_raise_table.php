<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaiseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raise', function (Blueprint $table) {
            $table->increments('RaiseId');
            $table->integer('ForumId');
            $table->integer('UserId');
            $table->dateTime('CreateTime');
            $table->dateTime('UpdateTime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raise');
    }
}
