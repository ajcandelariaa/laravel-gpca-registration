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
        Schema::table('main_spouses', function (Blueprint $table) {
            $table->boolean('day_one')->default(false);
            $table->boolean('day_two')->default(false);
            $table->boolean('day_three')->default(false);
            $table->boolean('day_four')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_spouses', function (Blueprint $table) {
            $table->dropColumn('day_one');
            $table->dropColumn('day_two');
            $table->dropColumn('day_three');
            $table->dropColumn('day_four');
        });
    }
};
