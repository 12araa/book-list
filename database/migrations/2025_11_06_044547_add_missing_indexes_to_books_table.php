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
        Schema::table('books', function (Blueprint $table) {
            $table->index('publisher');
            $table->index('avg_rating');
            $table->index('total_votes');
            $table->index('weighted_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['publisher']);
            $table->dropIndex(['avg_rating']);
            $table->dropIndex(['total_votes']);
            $table->dropIndex(['weighted_rating']);
        });
    }
};
