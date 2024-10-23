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
        Schema::table('printed_badges', function (Blueprint $table) {
            $table->boolean('collected')->default(false);
            $table->text('collected_by')->nullable();
            $table->dateTime('collected_marked_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('printed_badges', function (Blueprint $table) {
            $table->dropColumn('collected');
            $table->dropColumn('collected_by');
            $table->dropColumn('collected_marked_datetime');
        });
    }
};
