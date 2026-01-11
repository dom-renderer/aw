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
        Schema::table('product_tier_pricings', function (Blueprint $table) {
            $table->tinyInteger('pricing_type')->default(0)->after('product_additional_unit_id')->comment('0 = Tier Pricing | 1 = Single Price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_tier_pricings', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });
    }
};