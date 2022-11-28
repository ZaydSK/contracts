<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialAmountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_amounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('quantity');
            $table->bigInteger('not_used_quantity');
            $table->bigInteger('individual_price');
            $table->bigInteger('overall_price');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('contract_materials');
            $table->morphs('parentable');
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
        Schema::dropIfExists('material_amounts');
    }
}
