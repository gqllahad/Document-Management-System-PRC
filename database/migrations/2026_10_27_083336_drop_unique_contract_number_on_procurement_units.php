<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_units', function (Blueprint $table) {
            $table->dropUnique('procurement_units_contract_number_unique');
            $table->index(['project_id', 'contract_number']);
        });
    }

    public function down(): void
    {
        Schema::table('procurement_units', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'contract_number']);
            $table->unique('contract_number');
        });
    }
};
