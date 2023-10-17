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
            $table->boolean('attending_yf')->nullable();
            $table->boolean('attending_networking_dinner')->nullable();
            $table->boolean('attending_welcome_dinner')->nullable();
            $table->boolean('attending_gala_dinner')->nullable();
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
            $table->dropColumn('attending_yf');
            $table->dropColumn('attending_networking_dinner');
            $table->dropColumn('attending_welcome_dinner');
            $table->dropColumn('attending_gala_dinner');
        });
    }
};
