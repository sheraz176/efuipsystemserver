<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuperAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('super_agents', function (Blueprint $table) {
            $table->bigIncrements('super_agent_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('status')->nullable();
            $table->boolean('islogin')->default(false);
            $table->string('call_status')->nullable();
            $table->timestamp('today_login_time')->nullable();
            $table->timestamp('today_logout_time')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Foreign key constraint for company_id if needed
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('super_agents');
    }
}
