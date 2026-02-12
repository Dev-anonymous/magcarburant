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
        Schema::create('state_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->double('usd_cdf')->nullable();
            $table->double('cdf_usd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('state_rates');
    }
};
