<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procurement_unit_id')
                ->constrained('procurement_units')
                ->cascadeOnDelete();

            $table->string('item_name');
            $table->text('description')->nullable();

            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();

            $table->timestamps();

            $table->index('procurement_unit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_items');
    }
};
