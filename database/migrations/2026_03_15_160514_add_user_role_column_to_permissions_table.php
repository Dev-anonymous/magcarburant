<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['users_id']);
            $table->dropUnique('permissions_users_id_name_unique');
            $table->dropColumn('users_id');
            $table->string('user_role')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('user_role');
            $table->foreignId('users_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unique(['users_id', 'name']);
        });
    }
};
