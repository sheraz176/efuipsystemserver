<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InteresedCustomerData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table->id();
        $table->string('subscriber_msisdn')->nullable();
        $table->string('customer_cnic')->nullable();
        $table->unsignedBigInteger('plan_id')->nullable();
        $table->unsignedBigInteger('product_id')->nullable();
        $table->string('beneficiary_msisdn')->nullable();
        $table->string('beneficiary_cnic')->nullable();
        $table->string('beneficiary_name')->nullable();
        $table->unsignedBigInteger('agent_id')->nullable();
        $table->unsignedBigInteger('company_id')->nullable();
        $table->string('deduction_status')->nullable();
        $table->timestamps();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interesed_customer_data');
    }
}
