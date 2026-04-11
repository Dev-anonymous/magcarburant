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
        Schema::create('fuelpricemining', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('structurepricemining_id')->unsigned();
            $table->integer('fuel_id')->unsigned();
            $table->integer('labelmining_id')->unsigned();
            $table->integer('zone_id')->unsigned();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('currency')->nullable();

            $table->unique(['structurepricemining_id', 'zone_id', 'fuel_id', 'labelmining_id'], 'fpmining_unique');

            $table->foreign('fuel_id')->references('id')->on('fuel')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('labelmining_id')->references('id')->on('labelmining')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('zone_id')->references('id')->on('zone')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('structurepricemining_id')->references('id')->on('structurepricemining')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuelpricemining');
    }
};
