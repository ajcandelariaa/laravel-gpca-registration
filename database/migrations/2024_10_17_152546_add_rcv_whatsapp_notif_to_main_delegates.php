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
            $table->boolean('receive_whatsapp_notifications')->nullable();
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
            $table->dropColumn('receive_whatsapp_notifications');
        });
    }
};
