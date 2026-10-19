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
            $table->dropColumn('expenses_classes');

            $table->string('winning_bidder')->nullable();
            $table->unsignedSmallInteger('delivery_period')->nullable();

            $table->renameColumn(
                'delivered_date',
                'actual_date_of_delivery'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
             $table->renameColumn(
                'actual_date_of_delivery',
                'delivered_date'
            );


            $table->dropColumn([
                'winning_bidder',
                'delivery_period',
            ]);

            $table->string('expenses_classes', 10)->nullable();
        });
    }
};
