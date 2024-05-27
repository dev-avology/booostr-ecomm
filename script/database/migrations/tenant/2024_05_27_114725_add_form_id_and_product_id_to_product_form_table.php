<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFormIdAndProductIdToProductFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_form', function (Blueprint $table) {
            $table->integer('form_id')->nullable();
            $table->integer('product_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_form', function (Blueprint $table) {
            $table->dropColumn('form_id');
            $table->dropColumn('product_id');
        });
    }
}
