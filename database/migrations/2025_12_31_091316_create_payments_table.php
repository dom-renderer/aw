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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();

            $table->enum('payment_method', ['cash','credit_card','debit_card','bank_transfer','upi','wallet','cod','other']);
            $table->string('payment_gateway')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');

            $table->string('transaction_id')->nullable()->index();
            $table->enum('status', ['pending','processing','completed','failed','cancelled','refunded'])->default('pending');

            $table->json('gateway_response')->nullable();
            $table->decimal('refunded_amount', 15, 2)->default(0);
            $table->timestamp('refunded_at')->nullable();

            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();

            $table->timestamp('payment_date')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
