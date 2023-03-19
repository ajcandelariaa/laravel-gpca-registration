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
        Schema::create('additional_delegates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('main_delegate_id');
            $table->string('salutation')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email_address');
            $table->string('mobile_number');
            $table->string('nationality');
            $table->string('job_title');
            $table->string('pcode_used')->nullable();
            $table->timestamps();
            $table->foreign('main_delegate_id')->references('id')->on('main_delegates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_delegates');
    }
};
