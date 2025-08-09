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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('project_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->uuid('assigned_to')->nullable(); // User ID
            $table->uuid('created_by'); // User ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'review', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('attachments')->nullable(); // Store file paths/URLs
            $table->json('tags')->nullable(); // Store tags as JSON array
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'priority']);
            $table->index(['tenant_id', 'assigned_to']);
            $table->index(['tenant_id', 'created_by']);
            $table->index(['tenant_id', 'project_id']);
            $table->index(['tenant_id', 'category_id']);
            $table->index(['tenant_id', 'due_date']);
            
            // Add foreign keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
