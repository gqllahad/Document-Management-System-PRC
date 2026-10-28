<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('project_modifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('source_project_id')->nullable()->constrained('projects')->restrictOnDelete();

            $table->enum('type', ['abc_increase', 'abc_decrease']);
            $table->decimal('amount', 15, 2);
            $table->decimal('abc_before', 15, 2);
            $table->decimal('abc_after', 15, 2);

            $table->text('reason');
            $table->string('reference_no')->nullable();
            $table->date('approval_date');

            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_modifications');
    }
};
