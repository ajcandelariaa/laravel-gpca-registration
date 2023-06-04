<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('main_delegates', function (Blueprint $table) {
            $table->string('registration_method')->default('online');
            $table->mediumText('transaction_remarks')->nullable();
            
            $table->boolean('delegate_cancelled')->default(0);
            $table->boolean('delegate_replaced')->default(0);
            $table->boolean('delegate_refunded')->default(0);

            $table->unsignedBigInteger('delegate_replaced_by_id')->nullable();
            $table->dateTime('delegate_cancelled_datetime')->nullable();
            $table->dateTime('delegate_refunded_datetime')->nullable();
            $table->dateTime('delegate_replaced_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_delegates', function (Blueprint $table) {
            $table->dropColumn('registration_method');
            $table->dropColumn('transaction_remarks');

            $table->dropColumn('delegate_cancelled');
            $table->dropColumn('delegate_replaced');
            $table->dropColumn('delegate_refunded');
            
            $table->dropColumn('delegate_replaced_by_id');

            $table->dropColumn('delegate_cancelled_datetime');
            $table->dropColumn('delegate_refunded_datetime');
            $table->dropColumn('delegate_replaced_datetime');
        });
    }
};
