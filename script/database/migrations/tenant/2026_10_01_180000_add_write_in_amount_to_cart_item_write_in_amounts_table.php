<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWriteInAmountToCartItemWriteInAmountsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('cart_item_write_in_amounts')) {
            return;
        }

        Schema::create('cart_item_write_in_amounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cart_id', 64);
            $table->string('rowid', 64);
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('write_in_amount', 12, 2);
            $table->timestamps();
            $table->unique(['cart_id', 'rowid']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_item_write_in_amounts');
    }
}
