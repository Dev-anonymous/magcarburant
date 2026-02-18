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
        Schema::create('accounting_closures', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('entity_id');
            $table->date('closed_until');
            $table->unsignedBigInteger('closed_by');
            $table->timestamps();
            $table->foreign('entity_id')->references('id')->on('entity')->cascadeOnDelete();
            $table->foreign('closed_by')->references('id')->on('users');
            $table->unique(['entity_id', 'closed_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_closures');
    }
};
