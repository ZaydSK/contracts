<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillMaterialDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_material_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_material_id');
            $table->unsignedBigInteger('material_amount_id');
            $table->double('price');
            $table->double('quantity');
            $table->foreign('bill_material_id')->references('id')->on('bill_materials');
            $table->foreign('material_amount_id')->references('id')->on('material_amounts');
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
        Schema::dropIfExists('bill_material_details');
    }
}
