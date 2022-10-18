<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('branch');
            $table->text('content');
            $table->text('number');
            $table->text('executing_agency');
            $table->text('watching_agency');
            $table->date('date');
            $table->bigInteger('price');
            $table->bigInteger('up_price');
            $table->integer('up_percent');
            $table->integer('down_percent');
            $table->date('starting_date');
            $table->date('finishing_date');
            $table->text('execution_period');
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
        Schema::dropIfExists('contracts');
    }
};
