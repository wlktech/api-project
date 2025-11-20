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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Already indexed (unique constraint)
            $table->string('code')->unique(); // Already indexed (unique constraint)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Add indexes for common queries
            $table->index('is_active'); // For filtering active/inactive departments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
