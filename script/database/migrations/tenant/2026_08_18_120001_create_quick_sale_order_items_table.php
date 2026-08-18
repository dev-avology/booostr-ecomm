<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuickSaleOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('quick_sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('descriptor_id')->nullable()->index();
            $table->string('descriptor_name');
            $table->string('title');
            $table->decimal('unit_amount', 12, 2);
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('line_subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2);
            $table->string('order_invoice_no', 32)->nullable()->index();
            $table->dateTime('order_placed_at')->nullable()->index();
            $table->string('payment_method', 32)->nullable();
            $table->unsignedTinyInteger('order_from')->nullable();
            $table->unsignedTinyInteger('payment_status')->nullable();
            $table->unsignedBigInteger('wpuid')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('descriptor_id')
                ->references('id')
                ->on('quick_sale_descriptors')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quick_sale_order_items');
    }
}
