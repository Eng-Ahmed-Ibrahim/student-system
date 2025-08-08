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
            Schema::table('payments', function (Blueprint $table) {
            // احذف الـ foreign key القديم
            $table->dropForeign(['student_fee_id']);

            // أضف الـ foreign key الجديد بـ onDelete cascade
            $table->foreign('student_fee_id')
                ->references('id')
                ->on('student_fees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('payments', function (Blueprint $table) {
            // ارجعه زي ما كان (set null)
            $table->dropForeign(['student_fee_id']);
            $table->foreign('student_fee_id')
                ->references('id')
                ->on('student_fees')
                ->onDelete('set null');
        });
    }
};
