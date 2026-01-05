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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();

            $table->string('product_name')->nullable()->comment('snapshot');
            $table->string('product_sku')->nullable()->comment('snapshot');
            $table->string('variant_name')->nullable()->comment('snapshot');

            $table->tinyInteger('unit_type')->default(0)->comment('0 = Base Unit | 1 = Additional Unit');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('unit_name')->comment('snapshot');

            $table->enum('product_type', ['simple', 'variable', 'bundled'])->default('simple');

            $table->double('fulfilled_quantity')->default(0);
            $table->double('returned_quantity')->default(0);
            $table->enum('fulfillment_status', ['unfulfilled','partial','fulfilled','returned'])->default('unfulfilled');

            $table->double('quantity')->default(0);
            $table->double('price_per_unit')->default(0);
            $table->double('discount_amount')->default(0);
            $table->double('tax_amount')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('total')->default(0);

            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('restrict');
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
