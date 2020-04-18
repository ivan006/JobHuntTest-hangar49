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
          $table->string('first_name')->default("");
          $table->string('last_name')->default("");
          $table->string('email')->default("");
          $table->string('job_title_full')->default("");
          $table->string('job_title')->default("");
          $table->string('city')->default("");
          $table->string('country')->default("");
          $table->string('linkedin')->default("");
          $table->string('company')->default("");
          $table->string('company_website')->default("");
          $table->string('company_industry')->default("");
          $table->string('company_founded')->default("");
          $table->string('company_size')->default("");
          $table->string('company_linkedin')->default("");
          $table->string('company_headquarters')->default("");
          $table->string('email_reliability_status')->default("");
          $table->string('receiving_email_server')->default("");
          $table->string('kind')->default("");
          $table->string('tag')->default("");
          $table->string('month')->default("");
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
