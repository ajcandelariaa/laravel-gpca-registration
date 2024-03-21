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
        Schema::create('awards_participant_documents', function (Blueprint $table) {$table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('event_category');
            $table->unsignedBigInteger('participant_id');
            $table->string('document');
            $table->string('document_file_name');
            $table->string('document_type');

            $table->timestamps();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('participant_id')->references('id')->on('awards_main_participants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('awards_participant_documents');
    }
};
