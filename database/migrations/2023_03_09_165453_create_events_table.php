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
            $table->string('category');
            $table->string('name');
            $table->string('location');
            $table->string('description');
            $table->string('start_date');
            $table->string('end_date');
            $table->string('banner');
            $table->string('logo');

            $table->string('eb_end_date')->nullable();
            $table->decimal('member_eb_rate', 10, 2)->nullable();
            $table->decimal('nmember_eb_rate', 10, 2)->nullable();

            $table->string('std_start_date');
            $table->decimal('member_std_rate', 10, 2);
            $table->decimal('nmember_std_rate', 10, 2);
            
            $table->string('year');
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
