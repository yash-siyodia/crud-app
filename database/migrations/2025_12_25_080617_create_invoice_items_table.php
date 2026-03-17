<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('product_name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);

            $table->timestamps();
            $table->softDeletes(); // ✅ SOFT DELETE
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
