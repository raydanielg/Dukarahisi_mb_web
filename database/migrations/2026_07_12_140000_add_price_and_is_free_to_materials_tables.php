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
        foreach (['books', 'lesson_plans', 'syllabi', 'scheme_of_works', 'logbooks'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->decimal('price', 10, 2)->default(0)->after('description');
                $table->boolean('is_free')->default(false)->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['books', 'lesson_plans', 'syllabi', 'scheme_of_works', 'logbooks'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['price', 'is_free']);
            });
        }
    }
};
