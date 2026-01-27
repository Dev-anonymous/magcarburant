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
        Schema::table('sale', function (Blueprint $table) {
            $table->boolean('from_mutuality')->default(false);
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('sale')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['from_mutuality', 'parent_id']);
        });
    }
};
