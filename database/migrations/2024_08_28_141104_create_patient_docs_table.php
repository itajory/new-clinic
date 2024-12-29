<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('path')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('patient_docs');
            $table->foreignId('patient_id')->constrained('patients');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_docs');
    }
};
