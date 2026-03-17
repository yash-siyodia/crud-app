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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // 'created', 'updated', 'deleted', 'login', 'logout', etc.
            $table->string('model')->nullable(); // 'Product', 'User', 'Invoice', etc.
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the model instance
            $table->json('details')->nullable(); // Additional details about the action
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('action');
            $table->index('model');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
