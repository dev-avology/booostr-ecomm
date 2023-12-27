<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoouponForIdChangeToCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // // First, drop the existing column
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('coupon_for_id');
        });

        // Then, add the new column as a JSON column
        Schema::table('coupons', function (Blueprint $table) {
            $table->json('coupon_for_id')->nullable()->after('coupon_for_name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            //
        });
    }
}
