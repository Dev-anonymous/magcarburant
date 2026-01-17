<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveryfile', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('delivery_id');
            $table->string('file');

            $table->foreign('delivery_id')
                ->references('id')
                ->on('delivery')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveryfile');
    }
};
