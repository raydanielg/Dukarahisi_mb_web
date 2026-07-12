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
        foreach (['notes', 'books', 'lesson_notes', 'lesson_plans', 'syllabi', 'scheme_of_works', 'logbooks'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('topic_id')->nullable()->after('subject_id')->constrained()->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['notes', 'books', 'lesson_notes', 'lesson_plans', 'syllabi', 'scheme_of_works', 'logbooks'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['topic_id']);
                $table->dropColumn('topic_id');
            });
        }
    }
};
