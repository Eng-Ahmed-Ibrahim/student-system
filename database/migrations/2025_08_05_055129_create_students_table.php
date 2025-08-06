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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId("group_id")->constrained("groups")->onDelete("cascade");
            $table->string("student_code"); // group->code+1 , group->code+2
            $table->string("name");
            $table->string('phone');
            $table->string('parent_phone');
            $table->string("national_id");
            $table->text("address");
            $table->boolean("blocked")->default(false);
            $table->text('block_reason')->nullable();
            $table->string("discount")->nullable();
            $table->text("discount_reason")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
