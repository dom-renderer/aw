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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('warehouse_id');

            $table->enum('status', ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['pending','partial','paid','refunded','failed'])->default('pending');

            $table->double('subtotal')->default(0);
            $table->double('discount_amount')->default(0);
            $table->double('tax_amount')->default(0);
            $table->double('shipping_amount')->default(0);
            $table->double('total_amount')->default(0);
            $table->double('paid_amount')->default(0);
            $table->double('due_amount')->default(0);

            $table->string('currency', 3)->default('USD');

            $table->string('shipping_address_line_1')->nullable();
            $table->string('shipping_address_line_2')->nullable();
            $table->unsignedBigInteger('shipping_country_id')->nullable();
            $table->unsignedBigInteger('shipping_state_id')->nullable();
            $table->unsignedBigInteger('shipping_city_id')->nullable();
            $table->string('shipping_zipcode')->nullable();
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_email')->nullable();

            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->unsignedBigInteger('billing_country_id')->nullable();
            $table->unsignedBigInteger('billing_state_id')->nullable();
            $table->unsignedBigInteger('billing_city_id')->nullable();
            $table->string('billing_zipcode')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_email')->nullable();

            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
