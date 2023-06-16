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
        Schema::create('additional_spouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_spouse_id');
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email_address');
            $table->string('mobile_number');
            $table->string('nationality');
            $table->string('country');
            $table->string('city');

            $table->boolean('spouse_cancelled')->default(0);
            $table->boolean('spouse_replaced')->default(0);
            $table->boolean('spouse_refunded')->default(0);

            $table->string('spouse_replaced_type')->nullable();
            $table->unsignedBigInteger('spouse_original_from_id')->nullable();
            $table->unsignedBigInteger('spouse_replaced_from_id')->nullable();
            $table->unsignedBigInteger('spouse_replaced_by_id')->nullable();

            $table->dateTime('spouse_cancelled_datetime')->nullable();
            $table->dateTime('spouse_refunded_datetime')->nullable();
            $table->dateTime('spouse_replaced_datetime')->nullable();

            $table->timestamps();
            $table->foreign('main_spouse_id')->references('id')->on('main_spouses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_spouses');
    }
};
