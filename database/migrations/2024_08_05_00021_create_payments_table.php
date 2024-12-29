<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('payment_type', ['cash', 'visa', 'check', 'bank_transfer']);
            $table->decimal('amount', 10, 2);
            $table->string('attachment')->nullable();
            // $table->date('date'); there is no need
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('created_by')->constrained('users');

            // $table->foreignId('check_id')->nullable()->constrained('checks'); put it on checks
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
