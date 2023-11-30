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
        Schema::table('main_visitors', function (Blueprint $table) {
            $table->integer('email_broadcast_sent_count')->default(0);
            $table->dateTime('email_broadcast_sent_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_visitors', function (Blueprint $table) {
            $table->dropColumn('email_broadcast_sent_count');
            $table->dropColumn('email_broadcast_sent_datetime');
        });
    }
};
