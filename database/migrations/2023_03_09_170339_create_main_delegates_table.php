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
        Schema::create('main_delegates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('pass_type');
            $table->string('rate_type');
            $table->string('rate_type_string');

            $table->string('company_name');
            $table->string('company_sector');
            $table->string('company_address');
            $table->string('company_country');
            $table->string('company_city');
            $table->string('company_telephone_number')->nullable();
            $table->string('company_mobile_number');
            
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email_address');
            $table->string('mobile_number');
            $table->string('nationality');
            $table->string('job_title');
            $table->string('badge_type');
            $table->string('pcode_used')->nullable();

            $table->string('heard_where')->nullable();
            $table->string('quantity');
            $table->string('unit_price');
            $table->string('net_amount');
            $table->string('vat_price');
            $table->string('discount_price');
            $table->string('total_amount');
            $table->string('mode_of_payment');
            $table->string('registration_status');
            $table->string('payment_status');
            $table->dateTime('registered_date_time');
            $table->dateTime('paid_date_time')->nullable();
            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('main_delegates');
    }
};
