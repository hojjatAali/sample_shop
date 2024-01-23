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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger ('total_price');
            $table->bigInteger ('price_after_discount');
            $table->integer  ('discount')->nullable ();
            $table->bigInteger   ('shipping_cost')->nullable ();
            $table->string ('seller_name');
            $table->string ('customer_name');
            $table->string ('driver_name')->nullable ();
            $table->longText ('description')->nullable ();
            $table->longText ('address');
            $table->string ('recipient');
            $table->enum ("status",["in_progress","delivered","canceled","confirmation"])->default ("confirmation");
            $table->dateTime  ('delivered_at')->nullable ();
            $table->dateTime ('canceled_at')->nullable ();
            $table->dateTime ('payment_date')->nullable ();
            $table->date ('pay_deadline')->nullable ();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
