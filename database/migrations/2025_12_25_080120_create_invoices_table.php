<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();   // INV-2025-0001
            $table->string('customer_name');
            $table->date('invoice_date');

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2)->default(0);

            $table->string('status')->default('pending');

            $table->timestamps();
            $table->softDeletes(); // ✅ SOFT DELETE
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

