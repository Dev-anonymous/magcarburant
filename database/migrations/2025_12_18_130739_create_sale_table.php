<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sale', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->unsigned();
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

            $table->foreign('entity_id')
                ->references('id')->on('entity')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale');
    }
};
