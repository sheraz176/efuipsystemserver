<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_subscriptions', function (Blueprint $table) {
            $table->string('subscription_id')->primary();
            $table->string('customer_id');
            $table->integer('payer_cnic');
            $table->integer('payer_msisdn');
            $table->integer('subscriber_cnic');
            $table->integer('subscriber_msisdn');
            $table->string('beneficiary_name');
            $table->integer('beneficiary_msisdn');
            $table->integer('transaction_amount');
            $table->integer('transaction_status');
            $table->string('cps_originator_conversation_id');
            $table->string('cps_transaction_id');
            $table->string('cps_refund_transaction_id');
            $table->string('cps_response');
            $table->string('plan_id');
            $table->string('plan_code');
            $table->string('plan_status');
            $table->string('pulse');
            $table->string('api_source');
            $table->timestamp('recursive_charging_date')->nullable();
            $table->timestamp('subscription_time')->nullable();
            $table->timestamp('grace_period_time')->nullable();
            $table->string('sales_agent');
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
        Schema::dropIfExists('customer_subscriptions');
    }
}
