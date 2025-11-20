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
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('module_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        
            // Add indexes for faster queries
            $table->index('name'); // For searching/filtering by name
            $table->index('is_active'); // For filtering active/inactive features
            $table->index('module_id'); // IMPORTANT: For filtering features by module
            $table->index(['module_id', 'is_active']); // Composite index for common query patterns
            $table->index(['is_active', 'name']); // For filtering active features by name
            $table->index('created_at'); // For sorting by creation date
            
            // Foreign key constraint
            $table->foreign('module_id')->references('id')
                  ->on('modules')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
