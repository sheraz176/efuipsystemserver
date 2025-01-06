<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCliamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('msisdn'); // MSISDN field
            $table->unsignedBigInteger('plan_id'); // Foreign key for plan_id
            $table->unsignedBigInteger('product_id'); // Foreign key for product_id
            $table->enum('status', ['Approved', 'Reject', 'In Process'])->default('In Process'); // Enum field for status with default value
            $table->date('date')->nullable(); // Nullable date field
            $table->decimal('amount', 10, 2)->nullable(); // Nullable amount field with precision
            $table->enum('type', ['hospitalization', 'medical_and_lab_expense'])->nullable(); // Nullable enum for claim type
            $table->string('history_name')->nullable(); // Nullable history name field

            // Separate nullable fields for different document types
            $table->string('doctor_prescription')->nullable(); // Document for doctor prescription
            $table->string('medical_bill')->nullable(); // Document for medical bill
            $table->string('lab_bill')->nullable(); // Document for lab bill
            $table->string('other')->nullable(); // Document for other types

            $table->timestamps(); // Created at and updated at timestamps
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cliams');
    }
}
