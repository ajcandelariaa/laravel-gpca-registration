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
        Schema::table('main_visitors', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('city');

            $table->string('company_sector');
            $table->string('company_address');
            $table->string('company_country');
            $table->string('company_city');
            $table->string('company_telephone_number')->nullable();
            $table->string('company_mobile_number');
            $table->string('assistant_email_address')->nullable();
            $table->string('alternative_company_name')->nullable();

            $table->string('badge_type')->default('Visitor');
            $table->string('pcode_used')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_visitors', function (Blueprint $table) {
            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->dropColumn('company_sector');
            $table->dropColumn('company_address');
            $table->dropColumn('company_country');
            $table->dropColumn('company_city');
            $table->dropColumn('company_telephone_number');
            $table->dropColumn('company_mobile_number');
            $table->dropColumn('assistant_email_address');
            $table->dropColumn('alternative_company_name');
            
            $table->dropColumn('badge_type');
            $table->dropColumn('pcode_used');
        });
    }
};
