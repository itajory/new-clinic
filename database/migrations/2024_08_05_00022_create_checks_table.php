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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_id')->constrained('banks');
            $table->string('account_number');
            $table->string('check_number');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->enum('status', ['collected', 'returned', 'pending', 'replaced_with_check', 'replaced_with_cash']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->foreignId('replaced_by')->nullable()->constrained('checks');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
