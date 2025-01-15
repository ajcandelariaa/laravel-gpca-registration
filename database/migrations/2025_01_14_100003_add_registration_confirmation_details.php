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
        Schema::table('additional_delegates', function (Blueprint $table) {
            $table->integer('registration_confirmation_sent_count')->default(0);
            $table->dateTime('registration_confirmation_sent_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('additional_delegates', function (Blueprint $table) {
            $table->dropColumn('registration_confirmation_sent_count');
            $table->dropColumn('registration_confirmation_sent_datetime');
        });
    }
};
