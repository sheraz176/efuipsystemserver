<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
        $table->bigIncrements('product_id');
        $table->unsignedBigInteger('plan_id');
        $table->string('product_name');
        $table->integer('term_takaful');
        $table->integer('annual_hospital_cash_limit');
        $table->integer('accidental_medicial_reimbursement');
        $table->integer('contribution');
        $table->string('product_code');
        $table->integer('fee');
        $table->integer('autoRenewal');
        $table->integer('duration');
        $table->integer('status')->default('1');
        $table->string('scope_of_cover');
        $table->string('eligibility');
        $table->string('other_key_details');
        $table->string('exclusions');
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
        Schema::dropIfExists('products');
    }
}
