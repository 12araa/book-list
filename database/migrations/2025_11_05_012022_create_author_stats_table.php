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
        Schema::create('author_stats', function (Blueprint $table) {
            $table->foreignId('author_id')->primary()->constrained('authors')->onDelete('cascade');
            $table->integer('total_ratings')->default(0)->index(); // sebelumnya 'total_books'
            $table->decimal('avg_rating', 4, 2)->default(0)->index(); // sebelumnya (3, 2)
            $table->year('last_published_year')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_stats');
    }
};
