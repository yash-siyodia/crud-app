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
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('invoice_number');
            $table->index('created_at');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index('invoice_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['invoice_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
