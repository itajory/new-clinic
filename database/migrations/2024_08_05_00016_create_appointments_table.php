<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("appointments", function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->constrained("patients");
            $table->foreignId("doctor_id")->constrained("users");
            $table->foreignId("medical_center_id")->constrained("medical_centers");
            $table->foreignId("treatment_id")->constrained("treatments");
            $table->dateTime("appointment_time");
            $table->enum("status", [
                "reserved",
                "waiting",
                "completed",
                "not_attended_with_telling",
                "not_attended_without_telling",
            ]);
            $table->integer("duration");
            $table->integer("repeat")->default(0);
            $table->foreignId("repeat_id")->nullable()->constrained("appointments");
            $table->float("price")->default(0);
            $table->float("discount")->default(0);
            $table
                ->foreignId("patient_fund_id")
                ->nullable()
                ->constrained("patient_funds");
            $table
                ->enum("patient_fund_contribution_type", ["percentage", "fixed"])
                ->nullable();
            $table->float("patient_fund_amount")->default(0);
            $table->float("patient_fund_total")->default(0);
            $table->float("total")->default(0);
            $table->foreignId("created_by")->constrained("users");
            $table->string("note")->nullable();
            $table->boolean("is_patient_fund_closed")->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['medical_center_id', 'appointment_time']);
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('appointment_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("appointments");
    }
};
