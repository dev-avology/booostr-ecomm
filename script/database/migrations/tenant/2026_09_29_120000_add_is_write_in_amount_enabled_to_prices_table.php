<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsWriteInAmountEnabledToPricesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('prices', 'is_write_in_amount_enabled')) {
            Schema::table('prices', function (Blueprint $table) {
                $table->unsignedTinyInteger('is_write_in_amount_enabled')->default(0)->after('tax');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('prices', 'is_write_in_amount_enabled')) {
            Schema::table('prices', function (Blueprint $table) {
                $table->dropColumn('is_write_in_amount_enabled');
            });
        }
    }
}
