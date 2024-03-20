<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnsubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('unsubscriptions', function (Blueprint $table) {
        $table->bigIncrements('unsubscription_id')->primary(); // Custom primary key definition
        $table->timestamp('unsubscription_datetime'); // Date and time of unsubscription
        $table->string('medium'); // Medium through which unsubscription occurred
        $table->unsignedBigInteger('subscription_id'); // Foreign key referencing the subscription table
        $table->unsignedBigInteger('refunded_id')->nullable(); // Foreign key referencing the refunded table (if applicable)
        $table->timestamps(null, null); // Disable default created_at and updated_at columns
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unsubscriptions');
    }
}
