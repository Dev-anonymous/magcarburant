<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->unsigned();
            $table->date('date')->nullable();
            $table->string('product')->nullable();
            $table->string('provider')->nullable();
            $table->string('billnumber')->nullable();
            $table->decimal('unitprice', 12, 3)->nullable();
            $table->decimal('qtytm', 12, 3)->nullable();
            $table->decimal('qtym3', 12, 3)->nullable();
            $table->decimal('density', 12, 3)->nullable();

            $table->unique('billnumber');

            $table->foreign('entity_id')
                ->references('id')->on('entity')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase');
    }
};
