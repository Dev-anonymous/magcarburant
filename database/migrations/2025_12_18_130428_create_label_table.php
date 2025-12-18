<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('label', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label')->nullable();
            $table->string('tag', 5);

            $table->unique('tag');
            $table->unique('label');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label');
    }
};
