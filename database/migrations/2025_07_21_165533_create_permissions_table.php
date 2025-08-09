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
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // create_projects, read_projects, update_projects, delete_projects
            $table->string('display_name'); // Create Projects, Read Projects, etc.
            $table->text('description')->nullable();
            $table->string('resource'); // projects, tasks, categories, users, settings
            $table->string('action'); // create, read, update, delete, manage
            $table->timestamps();

            // Indexes
            $table->index(['resource', 'action']);
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
