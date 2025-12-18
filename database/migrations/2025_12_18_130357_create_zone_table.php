<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zone', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zone')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zone');
    }
};
