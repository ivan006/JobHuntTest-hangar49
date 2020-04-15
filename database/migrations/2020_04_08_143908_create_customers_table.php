<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('job_title_full');
            $table->string('job_title');
            $table->string('city');
            $table->string('country');
            $table->string('linkedin');
            $table->string('company');
            $table->string('company_website');
            $table->string('company_industry');
            $table->string('company_founded');
            $table->string('company_size');
            $table->string('company_linkedin');
            $table->string('company_headquarters');
            $table->string('email_reliability_status');
            $table->string('receiving_email_server');
            $table->string('kind');
            $table->string('tag');
            $table->string('month');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
