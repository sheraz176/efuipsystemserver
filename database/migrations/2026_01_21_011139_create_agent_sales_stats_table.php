<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSalesStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sales_stats', function (Blueprint $table) {
            $table->id();
               // 🔹 sirf agent_id ke against
            $table->unsignedBigInteger('agent_id')->unique();

            // 🔹 counters
            $table->unsignedInteger('today_sales')->default(0);
            $table->unsignedInteger('month_sales')->default(0);
            $table->unsignedInteger('year_sales')->default(0);

            // 🔹 tracking
            $table->date('stat_date')->nullable();
            $table->string('stat_month', 7)->nullable(); // YYYY-MM
            $table->year('stat_year')->nullable();

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
        Schema::dropIfExists('agent_sales_stats');
    }
}
