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
        Schema::create('delegate_details_update_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('event_category');

            $table->unsignedBigInteger('delegate_id');
            $table->string('delegate_type');

            $table->string('updated_by_name')->nullable();
            $table->string('updated_by_pc_number')->nullable();
            $table->text('description')->nullable();

            $table->dateTime('updated_date_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delegate_details_update_logs');
    }
};
