<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_units', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->restrictOnDelete();

            $table->string('unit_name')->default('Main Contract');

            $table->decimal('contract_price', 15, 2)->nullable();
            $table->string('contract_number')->nullable()->unique();
            $table->date('contract_date')->nullable();

            $table->string('winning_bidder')->nullable();

            $table->date('ntp_date')->nullable();
            $table->unsignedSmallInteger('delivery_period')->nullable();
            $table->date('actual_date_of_delivery')->nullable();

            $table->foreignId('status_id')
                ->nullable()
                ->constrained('statuses')
                ->nullOnDelete();

            $table->date('status_date')->nullable();
            $table->string('update')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_units');
    }
};
