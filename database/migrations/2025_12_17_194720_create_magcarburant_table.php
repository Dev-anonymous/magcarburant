<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entity', function (Blueprint $table) {
            $table->integer('id', true);
            $table->unsignedBigInteger('users_id')->index('fk_entity_users_idx');
            $table->string('shortname')->unique('shortname_unique');
            $table->string('longname');
            $table->string('logo')->nullable();
        });

        Schema::create('fuel', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('fuel')->unique('fuel_unique');
            $table->enum('fuel_type', ['terrestre', 'aviation'])->default('terrestre');
        });

        Schema::create('fuelprice', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('structureprice_id')->index('fk_fuelprice_structureprice1_idx');
            $table->integer('fuel_id')->index('fk_fuelprice_fuel1_idx');
            $table->integer('label_id')->index('fk_fuelprice_label1_idx');
            $table->integer('zone_id')->index('fk_fuelprice_zone1_idx');
            $table->decimal('amount', 12)->nullable();
            $table->string('currency')->nullable();

            $table->unique(['structureprice_id', 'zone_id', 'fuel_id', 'label_id'], 'unique_price');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('label', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('label')->nullable()->unique('label_unique');
            $table->string('tag', 5)->unique('tag_unique');
        });


        Schema::create('purchase', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('entity_id')->index('fk_purchase_entity1_idx');
            $table->date('date')->nullable();
            $table->string('product')->nullable();
            $table->string('provider')->nullable();
            $table->string('billnumber')->nullable()->unique('billnumber_unique');
            $table->double('unitprice')->nullable();
            $table->double('qtytm')->nullable();
            $table->double('qtym3')->nullable();
            $table->string('density')->nullable();
        });

        Schema::create('purchasefile', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('purchase_id')->index('fk_purchasefile_purchase1_idx');
            $table->string('file')->nullable();
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('entity_id')->nullable()->index('fk_rates_entity1_idx');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->double('usd_cdf')->nullable();
            $table->double('cdf_usd')->nullable();
        });

        Schema::create('sale', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('entity_id')->index('fk_sale_entity1_idx');
            $table->date('date')->nullable();
            $table->string('locality')->nullable();
            $table->string('way')->nullable();
            $table->string('product')->nullable();
            $table->string('delivery_note')->nullable();
            $table->string('delivery_program')->nullable();
            $table->string('client')->nullable();
            $table->string('lata')->nullable();
            $table->string('l15')->nullable();
            $table->string('density')->nullable();
        });

        Schema::create('salefile', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('sale_id')->index('fk_salefile_sale1_idx');
            $table->string('file')->nullable();
        });

        Schema::create('structureprice', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('entity_id')->index('fk_pricestructure_entity1_idx');
            $table->string('name')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->double('usd_cdf')->nullable();
            $table->double('cdf_usd')->nullable();
        });

        Schema::create('zone', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('zone')->nullable();
        });

        Schema::table('entity', function (Blueprint $table) {
            $table->foreign(['users_id'], 'fk_entity_users')->references(['id'])->on('users')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('fuelprice', function (Blueprint $table) {
            $table->foreign(['fuel_id'], 'fk_fuelprice_fuel1')->references(['id'])->on('fuel')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['label_id'], 'fk_fuelprice_label1')->references(['id'])->on('label')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['structureprice_id'], 'fk_fuelprice_structureprice1')->references(['id'])->on('structureprice')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['zone_id'], 'fk_fuelprice_zone1')->references(['id'])->on('zone')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('purchase', function (Blueprint $table) {
            $table->foreign(['entity_id'], 'fk_purchase_entity1')->references(['id'])->on('entity')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('purchasefile', function (Blueprint $table) {
            $table->foreign(['purchase_id'], 'fk_purchasefile_purchase1')->references(['id'])->on('purchase')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('rates', function (Blueprint $table) {
            $table->foreign(['entity_id'], 'fk_rates_entity1')->references(['id'])->on('entity')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('sale', function (Blueprint $table) {
            $table->foreign(['entity_id'], 'fk_sale_entity1')->references(['id'])->on('entity')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('salefile', function (Blueprint $table) {
            $table->foreign(['sale_id'], 'fk_salefile_sale1')->references(['id'])->on('sale')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('structureprice', function (Blueprint $table) {
            $table->foreign(['entity_id'], 'fk_pricestructure_entity1')->references(['id'])->on('entity')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('structureprice', function (Blueprint $table) {
            $table->dropForeign('fk_pricestructure_entity1');
        });

        Schema::table('salefile', function (Blueprint $table) {
            $table->dropForeign('fk_salefile_sale1');
        });

        Schema::table('sale', function (Blueprint $table) {
            $table->dropForeign('fk_sale_entity1');
        });

        Schema::table('rates', function (Blueprint $table) {
            $table->dropForeign('fk_rates_entity1');
        });

        Schema::table('purchasefile', function (Blueprint $table) {
            $table->dropForeign('fk_purchasefile_purchase1');
        });

        Schema::table('purchase', function (Blueprint $table) {
            $table->dropForeign('fk_purchase_entity1');
        });

        Schema::table('fuelprice', function (Blueprint $table) {
            $table->dropForeign('fk_fuelprice_fuel1');
            $table->dropForeign('fk_fuelprice_label1');
            $table->dropForeign('fk_fuelprice_structureprice1');
            $table->dropForeign('fk_fuelprice_zone1');
        });

        Schema::table('entity', function (Blueprint $table) {
            $table->dropForeign('fk_entity_users');
        });

        Schema::dropIfExists('zone');

        Schema::dropIfExists('users');

        Schema::dropIfExists('structureprice');

        Schema::dropIfExists('sessions');

        Schema::dropIfExists('salefile');

        Schema::dropIfExists('sale');

        Schema::dropIfExists('rates');

        Schema::dropIfExists('purchasefile');

        Schema::dropIfExists('purchase');

        Schema::dropIfExists('personal_access_tokens');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('label');

        Schema::dropIfExists('jobs');

        Schema::dropIfExists('job_batches');

        Schema::dropIfExists('fuelprice');

        Schema::dropIfExists('fuel');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('entity');

        Schema::dropIfExists('cache_locks');

        Schema::dropIfExists('cache');
    }
};
