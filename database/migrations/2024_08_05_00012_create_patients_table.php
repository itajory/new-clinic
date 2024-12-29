<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->string('id_number')->unique();
            $table->date('birth_date');
            $table->string('guardian_phone')->nullable();
            $table->string('patient_phone')->nullable();
            $table->foreignId('city_id')->constrained('cities');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
