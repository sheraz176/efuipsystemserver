<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterestedCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interested_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_msisdn');
            $table->string('customer_cnic');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('product_id');
            $table->string('beneficiary_msisdn');
            $table->string('beneficiary_cnic');
            $table->string('relationship');
            $table->unsignedBigInteger('agent_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('plan_id')->references('plan_id')->on('plans');
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->foreign('agent_id')->references('agent_id')->on('tele_sales_agents');
            $table->foreign('company_id')->references('id')->on('company_profile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interested_customers');
    }
}
