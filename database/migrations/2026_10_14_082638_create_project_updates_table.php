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
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained();
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->string('update');
            $table->date('status_date');
            $table->timestamps();

            $table->index(['project_id', 'status_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};
