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
            $table->uuid('id')->primary();
            $table->uuid('tenant_id')->nullable(); // null for central admin roles
            $table->string('name'); // owner, admin, manager, user
            $table->string('display_name'); // Owner, Administrator, Manager, User
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false); // default role for new users
            $table->boolean('is_system')->default(false); // system roles cannot be deleted
            $table->timestamps();

            // Indexes
            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'is_default']);
            
            // Foreign key
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            // Unique constraint: role name per tenant
            $table->unique(['tenant_id', 'name']);
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
