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
            $table->boolean('attending_plenary')->nullable();
            $table->boolean('attending_symposium')->nullable();
            $table->boolean('attending_solxchange')->nullable();
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
            $table->dropColumn('attending_plenary');
            $table->dropColumn('attending_symposium');
            $table->dropColumn('attending_solxchange');
        });
    }
};
