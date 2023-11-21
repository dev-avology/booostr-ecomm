<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationproductoptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variationproductoptions', function (Blueprint $table) {
            $table->unsignedBigInteger('price_id'); //varition_product id
            $table->unsignedBigInteger('productoption_id'); //group option id
            $table->unsignedBigInteger('category_id'); //group option id

            $table->foreign('price_id')
            ->references('id')->on('prices')
            ->onDelete('cascade'); 

            $table->foreign('productoption_id')
            ->references('id')->on('productoptions')
            ->onDelete('cascade'); 
            
            $table->foreign('category_id')
            ->references('id')->on('categories')
            ->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variationproductoptions');
    }
}
