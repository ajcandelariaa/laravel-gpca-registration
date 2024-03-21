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
        Schema::create('awards_additional_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_participant_id');

            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email_address');
            $table->string('mobile_number');
            $table->mediumText('address');
            $table->string('country');
            $table->string('city');
            $table->string('job_title');
            $table->string('nationality');

            $table->boolean('participant_cancelled')->default(0);
            $table->boolean('participant_replaced')->default(0);
            $table->boolean('participant_refunded')->default(0);

            $table->string('participant_replaced_type')->nullable();
            $table->unsignedBigInteger('participant_original_from_id')->nullable();
            $table->unsignedBigInteger('participant_replaced_from_id')->nullable();
            $table->unsignedBigInteger('participant_replaced_by_id')->nullable();

            $table->dateTime('participant_cancelled_datetime')->nullable();
            $table->dateTime('participant_refunded_datetime')->nullable();
            $table->dateTime('participant_replaced_datetime')->nullable();

            $table->timestamps();
            $table->foreign('main_participant_id')->references('id')->on('awards_main_participants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awards_additional_participants');
    }
};
