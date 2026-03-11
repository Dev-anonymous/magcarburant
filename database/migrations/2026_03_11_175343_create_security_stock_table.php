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
        Schema::create('security_stock', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->integer('entity_id')->unsigned();
            $table->foreign('entity_id')->references('id')->on('entity')->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('amount');
            $table->boolean('from_state')->default(0);
            $table->unique(['month', 'entity_id', 'from_state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_stock');
    }
};
