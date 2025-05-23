<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Constraint\Constraint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('recipe_ingredient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipes_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredients_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_2', 8 , 2)->nullable();
            $table->decimal('quantity_4', 8 , 2)->nullable();
            $table->decimal('quantity_8', 8 , 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredient');
    }
};
