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
        Schema::create('main_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('pass_type');
            $table->string('rate_type');
            $table->string('rate_type_string');
            
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
            $table->dateTime('confirmation_date_time')->nullable();
            $table->string('confirmation_status')->nullable();

            $table->string('registration_method')->default('online');
            $table->mediumText('transaction_remarks')->nullable();
            
            $table->boolean('visitor_cancelled')->default(0);
            $table->boolean('visitor_replaced')->default(0);
            $table->boolean('visitor_refunded')->default(0);

            $table->unsignedBigInteger('visitor_replaced_by_id')->nullable();
            $table->dateTime('visitor_cancelled_datetime')->nullable();
            $table->dateTime('visitor_refunded_datetime')->nullable();
            $table->dateTime('visitor_replaced_datetime')->nullable();

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
        Schema::dropIfExists('main_visitors');
    }
};
