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
            $table->string('link');
            $table->mediumText('description');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->integer('event_vat');
            $table->string('banner');
            $table->string('logo');

            $table->date('eb_end_date')->nullable();
            $table->decimal('eb_member_rate', 10, 2)->nullable();
            $table->decimal('eb_nmember_rate', 10, 2)->nullable();

            $table->date('std_start_date');
            $table->decimal('std_member_rate', 10, 2);
            $table->decimal('std_nmember_rate', 10, 2);

            $table->string('badge_footer_link');
            $table->string('badge_footer_link_color');
            $table->string('badge_footer_bg_color');
            $table->string('badge_front_banner');
            $table->string('badge_back_banner');
            
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
