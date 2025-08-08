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
        Schema::table('student_fees', function (Blueprint $table) {
            $table->integer('discount')->nullable();
            $table->decimal("final_amount", 8, 2)->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('attendance_id')->nullable();

            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropColumn(['discount','final_amount','date','attendance_id']);
            $table->dropForeign(['attendance_id']);

        });
    }
};
