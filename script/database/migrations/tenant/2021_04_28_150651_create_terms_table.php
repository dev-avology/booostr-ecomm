<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string("full_id")->nullable();
            $table->string('title');
            $table->string('slug');
            $table->string('type');
            $table->integer('is_variation')->default(0);
            $table->integer('status')->default(1);
            $table->double('featured')->nullable();
            $table->double('rating')->nullable();
            $table->integer('list_type')->default(0)->comment("0->all,1->store,2->pos");
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
        Schema::dropIfExists('terms');
    }
}
