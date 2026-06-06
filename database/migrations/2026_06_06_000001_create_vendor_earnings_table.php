<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('gross_amount', 12, 2);   // order subtotal (excl. delivery)
            $table->decimal('commission', 12, 2);      // platform fee
            $table->decimal('net_amount', 12, 2);      // vendor receives
            $table->enum('status', ['pending', 'settled', 'withdrawn'])->default('pending');
            $table->timestamps();

            $table->unique('order_id'); // one earning record per order
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_earnings');
    }
};
