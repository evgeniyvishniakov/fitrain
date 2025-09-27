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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // RUB, USD, EUR
            $table->string('name'); // Российский рубль, US Dollar, Euro
            $table->string('symbol'); // ₽, $, €
            $table->string('symbol_position')->default('after'); // before, after
            $table->integer('decimal_places')->default(2);
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // курс к базовой валюте
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};