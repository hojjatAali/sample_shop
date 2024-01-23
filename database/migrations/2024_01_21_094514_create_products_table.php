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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string ('product_name');
            $table->bigInteger ('barcode')->nullable ();
            $table->date ('expire_date')->nullable ();
            $table->date ('produce_date')->nullable ();
            $table->integer ('max_in_card')->default (100);
            $table->bigInteger ('purchase_price');
            $table->bigInteger ('selling_price');
            $table->bigInteger ('customer_price')->nullable ();
            $table->integer ('quantity_in_box')->nullable ();
            $table->boolean ('is_active')->default (true);
            $table->enum ('package_type',["sack","package","carton","box"])->default ('carton');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
