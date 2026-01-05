<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('fulfillment_number')->unique();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['pending','processing','packed','shipped','delivered','cancelled'])->default('pending');

            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_url', 500)->nullable();

            $table->unsignedBigInteger('processed_by')->nullable();
            $table->unsignedBigInteger('packed_by')->nullable();
            $table->unsignedBigInteger('shipped_by')->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('packed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_fulfillments');
    }
};
