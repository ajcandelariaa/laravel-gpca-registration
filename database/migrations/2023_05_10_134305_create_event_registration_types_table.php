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
        Schema::create('event_registration_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('event_category');
            $table->string('registration_type');
            $table->string('badge_footer_front_name');
            $table->string('badge_footer_front_bg_color');
            $table->string('badge_footer_front_text_color');
            $table->string('badge_footer_back_name');
            $table->string('badge_footer_back_bg_color');
            $table->string('badge_footer_back_text_color');
            $table->boolean('active');
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
        Schema::dropIfExists('event_registration_types');
    }
};
