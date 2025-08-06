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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId("student_id")->constrained('students')->onDelete("cascade");
            $table->date("date");
            $table->time("time")->nullable();
            $table->time("class_start_at");
            $table->boolean('status')->default(0);
            $table->index(['date', 'student_id'], 'date_student_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
