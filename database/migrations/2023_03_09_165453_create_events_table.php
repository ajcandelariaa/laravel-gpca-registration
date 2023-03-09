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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('name');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('location');
            $table->string('member_eb_rate');
            $table->string('nmember_eb_rate');
            $table->string('member_std_rate');
            $table->string('nmember_std_rate');
            $table->string('description');
            $table->string('banner');
            $table->string('logo');
            $table->boolean('active');
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
        Schema::dropIfExists('events');
    }
};
