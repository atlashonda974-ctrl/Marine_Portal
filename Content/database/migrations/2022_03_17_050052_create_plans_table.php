<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {

            $table->id();
            $table->string('fplan')->nullable();
            $table->string('froom')->nullable();
            $table->string('fhosp')->nullable();
            $table->string('fnrmdelv')->nullable();
            $table->string('fcsec')->nullable();
            $table->string('fvlddat')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
