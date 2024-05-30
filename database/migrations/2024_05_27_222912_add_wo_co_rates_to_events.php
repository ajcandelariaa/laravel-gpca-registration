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
        Schema::table('events', function (Blueprint $table) {
            $table->date('wo_eb_end_date')->nullable();
            $table->decimal('wo_eb_full_member_rate', 10, 2)->nullable();
            $table->decimal('wo_eb_member_rate', 10, 2)->nullable();
            $table->decimal('wo_eb_nmember_rate', 10, 2)->nullable();

            $table->date('wo_std_start_date')->nullable();
            $table->decimal('wo_std_full_member_rate', 10, 2)->nullable();
            $table->decimal('wo_std_member_rate', 10, 2)->nullable();
            $table->decimal('wo_std_nmember_rate', 10, 2)->nullable();

            $table->date('co_eb_end_date')->nullable();
            $table->decimal('co_eb_full_member_rate', 10, 2)->nullable();
            $table->decimal('co_eb_member_rate', 10, 2)->nullable();
            $table->decimal('co_eb_nmember_rate', 10, 2)->nullable();

            $table->date('co_std_start_date')->nullable();
            $table->decimal('co_std_full_member_rate', 10, 2)->nullable();
            $table->decimal('co_std_member_rate', 10, 2)->nullable();
            $table->decimal('co_std_nmember_rate', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('wo_eb_end_date');
            $table->dropColumn('wo_eb_full_member_rate');
            $table->dropColumn('wo_eb_member_rate');
            $table->dropColumn('wo_eb_nmember_rate');

            $table->dropColumn('wo_std_start_date');
            $table->dropColumn('wo_std_full_member_rate');
            $table->dropColumn('wo_std_member_rate');
            $table->dropColumn('wo_std_nmember_rate');

            $table->dropColumn('co_eb_end_date');
            $table->dropColumn('co_eb_full_member_rate');
            $table->dropColumn('co_eb_member_rate');
            $table->dropColumn('co_eb_nmember_rate');

            $table->dropColumn('co_std_start_date');
            $table->dropColumn('co_std_full_member_rate');
            $table->dropColumn('co_std_member_rate');
            $table->dropColumn('co_std_nmember_rate');
        });
    }
};
