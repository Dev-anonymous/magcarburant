<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->unsigned()->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->double('usd_cdf')->nullable();
            $table->double('cdf_usd')->nullable();

            $table->foreign('entity_id')
                ->references('id')->on('entity')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
