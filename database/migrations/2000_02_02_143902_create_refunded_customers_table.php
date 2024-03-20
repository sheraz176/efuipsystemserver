<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundedCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refunded_customers', function (Blueprint $table) {
            $table->bigIncrements('refund_id')->primary(); // Primary Key
            $table->unsignedBigInteger('subscription_id'); // Foreign key referencing the subscription table
            $table->unsignedBigInteger('unsubscription_id'); // Foreign key referencing the unsubscriptions table
            $table->string('transaction_id'); // Transaction ID
            $table->string('reference_id'); // Reference ID
            $table->string('cps_response'); // CPS Response
            $table->string('result_description'); // Result Description
            $table->string('result_code'); // Result Code
            $table->string('refunded_by'); // Refunded By
            $table->string('medium'); // Medium through which refund occurred
            $table->timestamp('refunded_time'); // Date and time of the refund
            $table->timestamps(); // created_at and updated_at columns

            // Foreign key constraints
            // $table->foreign('subscription_id')->references('subscription_id')->on('unsubscriptions')->onDelete('cascade');
            // $table->foreign('unsubscription_id')->references('unsubscription_id')->on('unsubscriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refunded_customers');
    }
}
