<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('procurement_unit_updates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_unit_id')
                ->constrained('procurement_units')
                ->cascadeOnDelete();

            $table->foreignId('division_id')
                ->constrained('divisions');

            $table->foreignId('status_id')
                ->constrained('statuses');

            $table->foreignId('created_by')
                ->constrained('users');

            $table->string('update');
            $table->date('status_date');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_unit_updates');
    }
};
