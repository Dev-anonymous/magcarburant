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
        Schema::create('errors', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->text('message')->nullable();
            $table->text('source')->nullable();
            $table->integer('line')->nullable();
            $table->integer('column')->nullable();

            $table->longText('stack')->nullable();

            $table->text('url')->nullable();
            $table->text('user_agent')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip', 45)->nullable();

            $table->longText('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('errors');
    }
};
