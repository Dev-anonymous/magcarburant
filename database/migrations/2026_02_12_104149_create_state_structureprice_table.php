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
        Schema::create('state_structureprice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
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
        Schema::dropIfExists('state_structureprice');
    }
};
