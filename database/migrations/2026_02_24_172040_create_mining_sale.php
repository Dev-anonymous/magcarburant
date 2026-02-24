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
        Schema::create('mining_sale', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->unsigned();
            $table->string('terminal')->nullable();
            $table->date('date')->nullable();
            $table->string('locality')->nullable();
            $table->string('way')->nullable();
            $table->string('product')->nullable();
            $table->string('delivery_note')->nullable();
            $table->string('delivery_program')->nullable();
            $table->string('client')->nullable();
            $table->decimal('lata', 12, 3)->nullable();
            $table->decimal('l15', 12, 3)->nullable();
            $table->decimal('density', 12, 3)->nullable();
            $table->boolean('from_state')->default(0);
            $table->boolean('from_mutuality')->default(false);
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('mining_sale')->cascadeOnDelete();

            $table->foreign('entity_id')
                ->references('id')->on('entity')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mining_sale');
    }
};
