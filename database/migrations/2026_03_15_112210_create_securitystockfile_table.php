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
        Schema::create('securitystockfile', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('security_stock_id');
            $table->string('file')->nullable();

            $table->foreign('security_stock_id')
                ->references('id')
                ->on('security_stock')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('securitystockfile');
    }
};
