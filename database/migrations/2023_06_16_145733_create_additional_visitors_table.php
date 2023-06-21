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
        Schema::create('additional_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_visitor_id');
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email_address');
            $table->string('mobile_number');
            $table->string('nationality');
            $table->string('country');
            $table->string('city');
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();

            $table->boolean('visitor_cancelled')->default(0);
            $table->boolean('visitor_replaced')->default(0);
            $table->boolean('visitor_refunded')->default(0);

            $table->string('visitor_replaced_type')->nullable();
            $table->unsignedBigInteger('visitor_original_from_id')->nullable();
            $table->unsignedBigInteger('visitor_replaced_from_id')->nullable();
            $table->unsignedBigInteger('visitor_replaced_by_id')->nullable();

            $table->dateTime('visitor_cancelled_datetime')->nullable();
            $table->dateTime('visitor_refunded_datetime')->nullable();
            $table->dateTime('visitor_replaced_datetime')->nullable();

            $table->timestamps();
            $table->foreign('main_visitor_id')->references('id')->on('main_visitors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_visitors');
    }
};
