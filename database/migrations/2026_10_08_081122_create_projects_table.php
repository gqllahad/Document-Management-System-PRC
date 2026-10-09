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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('division_id')->constrained('divisions');
            $table->foreignId('status_id')->constrained('statuses');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
;

            $table->string('project_name');
            $table->decimal('approved_budget', 15, 2);
            $table->decimal('contract_price', 15, 2)->nullable();

            $table->string('contract_number')->unique();
            $table->date('contract_date')->nullable();
            $table->date('delivered_date')->nullable();

            $table->enum('quarter',['EPA/1ST', 'EPA/2ND', '1ST', '2ND', '3RD', '4TH']);
            $table->enum('update', ['installed', 'renewed', 'delivered', 'successful_bid', 'cancelled', 'pending', 'obligated'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
