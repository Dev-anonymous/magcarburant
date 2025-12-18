<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entity', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('users_id');
            $table->string('shortname');
            $table->string('longname');
            $table->string('logo')->nullable();

            $table->unique('shortname');

            $table->foreign('users_id')
                ->references('id')->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity');
    }
};
