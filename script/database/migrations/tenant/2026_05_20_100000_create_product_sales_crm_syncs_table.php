<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSalesCrmSyncsTable extends Migration
{
    public function up()
    {
        Schema::create('product_sales_crm_syncs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->enum('sync_type', ['one_time', 'continuous'])->default('continuous');
            $table->enum('sync_mode', ['all_results', 'current_page'])->default('all_results');
            $table->boolean('continuous_sync_enabled')->default(false)->index();
            $table->boolean('is_ticket_product')->default(false);
            $table->text('contact_tags')->nullable();
            $table->string('crm_list_name')->nullable();
            $table->json('filter_state')->nullable();
            $table->enum('sync_status', ['active', 'stopped', 'syncing', 'completed'])->default('active')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_processed_at')->nullable();
            $table->unsignedBigInteger('last_processed_record_id')->nullable();
            $table->unsignedInteger('total_synced_contacts')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'continuous_sync_enabled', 'sync_status'], 'product_crm_sync_lookup');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_sales_crm_syncs');
    }
}
