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
            $table->string('product', 32);
            $table->date('month');
            $table->decimal('avg_price', 12, 3)->default(0);
            $table->unique(['product', 'month']);
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
