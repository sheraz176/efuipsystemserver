<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsufficientBalanceCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insufficient_balance_customers', function (Blueprint $table) {
            $table->id('request_id')->primary(); // Change the primary key column name to request_id
            $table->string('transactionId');
            $table->timestamp('timeStamp');
            $table->string('resultCode');
            $table->string('resultDesc');
            $table->text('failedReason');
            $table->decimal('amount', 10, 2);
            $table->string('referenceId');
            $table->string('accountNumber');
            $table->string('type');
            $table->string('remark');
            $table->integer('planId');
            $table->integer('product_id');
            $table->integer('agent_id');
            $table->timestamp('sale_request_time')->nullable();
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
        Schema::dropIfExists('insufficient_balance_customers');
    }
}
