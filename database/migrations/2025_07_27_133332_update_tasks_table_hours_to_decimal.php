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
        Schema::table('tasks', function (Blueprint $table) {
            // Change hours columns from integer to decimal(5,1) to support 1 decimal place
            $table->decimal('estimated_hours', 5, 1)->nullable()->change();
            $table->decimal('actual_hours', 5, 1)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Revert to integer
            $table->integer('estimated_hours')->nullable()->change();
            $table->integer('actual_hours')->nullable()->change();
        });
    }
};
