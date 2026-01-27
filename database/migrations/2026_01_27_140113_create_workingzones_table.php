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
        Schema::create('workingzones', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('entity_id');
            $table->unsignedInteger('zone_id');

            $table->unique(['entity_id', 'zone_id']);

            $table->foreign('entity_id')
                ->references('id')
                ->on('entity')
                ->onDelete('cascade');

            $table->foreign('zone_id')
                ->references('id')
                ->on('zone')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workingzones');
    }
};
