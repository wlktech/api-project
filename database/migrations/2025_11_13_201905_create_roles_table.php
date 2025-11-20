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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Display name: "Super Admin"
            $table->string('code')->unique(); // System code: "super_admin"
            $table->unsignedBigInteger('department_id')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('cascade'); // or 'set null' depending on your needs
            
            // Note: created_at index likely unnecessary for roles table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
