<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->integer('number');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->date('date');
            $table->bigInteger('price');
            $table->bigInteger('up_price');
            $table->bigInteger('discount');
            $table->bigInteger('discount_of_executing_agency_price');
            $table->bigInteger('executing_agency_price');
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
        Schema::dropIfExists('bills');
    }
}
