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
        Schema::create('average_fuel_prices', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->string('product');
            $table->integer('zone_id')->unsigned();
            $table->decimal('avg_price', 12, 3);
            $table->unique(['month', 'product', 'zone_id']);
            $table->foreign('zone_id')->references('id')->on('zone')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('average_fuel_prices');
    }
};
