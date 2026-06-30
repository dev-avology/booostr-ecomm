<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_sales_crm_syncs', function (Blueprint $table) {
            if (!Schema::hasColumn('product_sales_crm_syncs', 'crm_group_id')) {
                $table->string('crm_group_id')->nullable()->after('crm_list_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_sales_crm_syncs', function (Blueprint $table) {
            if (Schema::hasColumn('product_sales_crm_syncs', 'crm_group_id')) {
                $table->dropColumn('crm_group_id');
            }
        });
    }
};
