<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery', function (Blueprint $table) {
            $table->increments('id');

            $table->string('terminal')->nullable();
            $table->unsignedInteger('entity_id');

            $table->date('date')->nullable();
            $table->string('locality')->nullable();
            $table->string('way')->nullable();
            $table->string('product')->nullable();
            $table->string('delivery_note')->nullable();
            $table->string('delivery_program')->nullable();
            $table->string('client')->nullable();

            // Nouveaux champs
            $table->decimal('qtym3', 12, 3)->nullable();
            $table->decimal('unitprice', 12, 3)->nullable();

            // Clé étrangère
            $table->foreign('entity_id')
                ->references('id')
                ->on('entity')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery');
    }
};
