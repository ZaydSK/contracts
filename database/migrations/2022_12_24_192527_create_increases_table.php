<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncreasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('increases', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->double('price');
            $table->string('period');
            $table->date('date');
            $table->unsignedBigInteger('contract_id');
            $table->json('materials');
            $table->foreign('contract_id')->references('id')->on('contracts');
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
        Schema::dropIfExists('increases');
    }
}
