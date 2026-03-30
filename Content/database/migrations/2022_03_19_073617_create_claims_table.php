<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('fref')->nullable();
            $table->string('femp')->nullable();
            $table->string('fintdat')->nullable();
            $table->string('fadmtdat')->nullable();
            $table->string('fdisdat')->nullable();
            $table->string('fdagns')->nullable();
            $table->string('fcvrtyp')->nullable(); 
            $table->string('fclmsts')->nullable();
            $table->string('fcrdhol')->nullable();

            $table->string('fpatnam')->nullable();
            $table->string('fplan')->nullable();
            $table->string('fdob')->nullable();
            $table->string('fage')->nullable();
            $table->string('fbillamt')->nullable();
            $table->string('fdetct')->nullable();
            $table->string('froomamt')->nullable();

            $table->string('fmatamt')->nullable();
            $table->string('fcsecamt')->nullable();
            $table->string('fadmtday')->nullable();
            $table->string('fpaidamt')->nullable();
            $table->string('fhosp')->nullable();
            $table->string('fhospdsc')->nullable();

            $table->string('fgrd')->nullable();
            $table->string('fmng')->nullable();
            $table->string('fpaiddat')->nullable();
            $table->string('ffile')->nullable();
            $table->string('fuserid')->nullable();
            $table->string('fupdateid')->nullable();
            $table->string('frem')->nullable();
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
        Schema::dropIfExists('claims');
    }
}
