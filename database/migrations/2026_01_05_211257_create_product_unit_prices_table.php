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
        Schema::create('product_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_variant_id')->nullable()->index();
            $table->unsignedBigInteger('unit_type')->default(0)->comment('0 = Base Unit | 1 = Additional Unit');
            $table->unsignedBigInteger('product_additional_unit_id')->comment('References product_base_units.id or product_additional_units.id')->index();
            $table->double('price_per_unit')->default(0);
            $table->boolean('discount_type')->default(1)->comment('0 = Fixed | 1 = Percentage');
            $table->double('discount_amount')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_unit_prices');
    }
};
