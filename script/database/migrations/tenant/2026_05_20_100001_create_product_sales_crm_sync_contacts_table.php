<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSalesCrmSyncContactsTable extends Migration
{
    public function up()
    {
        Schema::create('product_sales_crm_sync_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_sales_crm_sync_id')->index('crm_sync_contacts_sync_id');
            $table->unsignedBigInteger('product_id')->index();
            $table->enum('source_type', ['event_ticket', 'orderitem']);
            $table->unsignedBigInteger('source_id');
            $table->string('email')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['product_sales_crm_sync_id', 'source_type', 'source_id'],
                'crm_sync_contact_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_sales_crm_sync_contacts');
    }
}
