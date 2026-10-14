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
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('quarter', ['EPA/1ST', 'EPA/2ND', '1ST', '2ND', '3RD', '4TH'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->enum('quarter', ['EPA/1ST', 'EPA/2ND', '1ST', '2ND', '3RD', '4TH'])->nullable(false)->change();
        });
    }
};
