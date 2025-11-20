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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // Already indexed (unique constraint)
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            // Add indexes for faster queries
            $table->index('name'); // For searching/filtering by name
            $table->index('is_active'); // For filtering active/inactive modules
            $table->index(['is_active', 'name']); // Composite index for common query patterns
            $table->index('created_at'); // For sorting by creation date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
