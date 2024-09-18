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
            $table->string('title');
            $table->string('slug');
            $table->foreignId('category_id')->constrained()->onDelete('CASCADE');
            $table->text('ingredients');
            $table->text('method');
            $table->text('tips');
            $table->integer('energy');
            $table->integer('carbohydrate');
            $table->integer('protein');
            $table->string('thumbnail');
            $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
            $table->timestamp('created_at');
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
