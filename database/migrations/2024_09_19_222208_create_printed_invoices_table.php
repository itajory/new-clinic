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
        Schema::create('printed_invoices', function (Blueprint $table) {
            $table->id();
            $table->json('appointment_ids');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printed_invoices');
    }
};
