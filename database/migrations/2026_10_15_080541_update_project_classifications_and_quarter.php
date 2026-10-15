<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type', 30)->nullable();
            $table->string('source_funds', 20)->nullable();
            $table->string('expenses_classes', 10)->nullable();
        });

        // Allow old and new quarter values temporarily.
        DB::statement("
            ALTER TABLE projects
            MODIFY quarter ENUM(
                'EPA/1ST',
                'EPA/2ND',
                'EPA',
                '1ST',
                '2ND',
                '3RD',
                '4TH'
            ) NULL
        ");

        // Convert old EPA values to EPA.
        DB::statement("
            UPDATE projects
            SET quarter = 'EPA'
            WHERE quarter IN ('EPA/1ST', 'EPA/2ND')
        ");

        // Keep only the new quarter values.
        DB::statement("
            ALTER TABLE projects
            MODIFY quarter ENUM(
                'EPA',
                '1ST',
                '2ND',
                '3RD',
                '4TH'
            ) NULL
        ");
    }

    public function down(): void
    {
        // Restore the old quarter options.
        DB::statement("
            ALTER TABLE projects
            MODIFY quarter ENUM(
                'EPA/1ST',
                'EPA/2ND',
                'EPA',
                '1ST',
                '2ND',
                '3RD',
                '4TH'
            ) NULL
        ");

        DB::statement("
            UPDATE projects
            SET quarter = 'EPA/1ST'
            WHERE quarter = 'EPA'
        ");

        DB::statement("
            ALTER TABLE projects
            MODIFY quarter ENUM(
                'EPA/1ST',
                'EPA/2ND',
                '1ST',
                '2ND',
                '3RD',
                '4TH'
            ) NULL
        ");

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'project_type',
                'source_funds',
                'expenses_classes',
            ]);
        });
    }
};