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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('isbn')->index();
            $table->string('publisher');
            $table->year('publication_year')->index();
            $table->enum('availability', ['available','rented','reserved'])->default('available')->index();
            $table->string('store_location')->index();
            $table->foreignId('author_id')
                  ->constrained('authors')
                  ->onDelete('restrict')
                  ->index();
            $table->decimal('avg_rating', 4,2)->default(0);
            $table->integer('total_votes')->default(0);
            $table->decimal('weighted_rating',6,4)->default(0);
            $table->boolean('rating_trend_flag')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
