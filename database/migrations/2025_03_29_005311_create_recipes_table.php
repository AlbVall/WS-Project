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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meal_type'); // breakfast, lunch, dinner, snack
            $table->integer('calories');
            $table->integer('protein')->nullable();
            $table->integer('carbs')->nullable();
            $table->integer('fat')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('prep_time')->nullable(); // in minutes
            $table->integer('cook_time')->nullable(); // in minutes
            $table->integer('servings')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
