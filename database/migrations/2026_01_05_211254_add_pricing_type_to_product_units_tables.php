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
        Schema::table('product_base_units', function (Blueprint $table) {
            $table->enum('pricing_type', ['tier', 'non-tier'])->default('tier')->after('is_default_selling_unit');
        });

        Schema::table('product_additional_units', function (Blueprint $table) {
            $table->enum('pricing_type', ['tier', 'non-tier'])->default('tier')->after('is_default_selling_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_base_units', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });

        Schema::table('product_additional_units', function (Blueprint $table) {
            $table->dropColumn('pricing_type');
        });
    }
};
