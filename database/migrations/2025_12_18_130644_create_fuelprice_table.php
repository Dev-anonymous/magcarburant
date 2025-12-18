<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fuelprice', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('structureprice_id')->unsigned();
            $table->integer('fuel_id')->unsigned();
            $table->integer('label_id')->unsigned();
            $table->integer('zone_id')->unsigned();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency')->nullable();

            $table->unique(['structureprice_id', 'zone_id', 'fuel_id', 'label_id']);

            $table->foreign('fuel_id')->references('id')->on('fuel')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('label_id')->references('id')->on('label')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('zone_id')->references('id')->on('zone')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('structureprice_id')->references('id')->on('structureprice')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuelprice');
    }
};
