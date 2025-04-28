<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSMSMsisdnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_m_s_msisdns', function (Blueprint $table) {
            $table->id();
            $table->string('msisdn'); // MSISDN field
            $table->unsignedBigInteger('plan_id'); // Foreign key for plan_id
            $table->unsignedBigInteger('product_id'); // Foreign key for product_id
            $table->enum('status', ['1', '0'])->default('0'); // Enum field for status with default value
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
        Schema::dropIfExists('s_m_s_msisdns');
    }
}
