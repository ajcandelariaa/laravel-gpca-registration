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
            $table->unsignedBigInteger('delegate_original_from_id')->nullable();
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
            $table->dropColumn('delegate_original_from_id');
        });
    }
};
