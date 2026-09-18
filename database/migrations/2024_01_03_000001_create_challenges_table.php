<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['problem_solving', 'puzzle_logic'])->default('puzzle_logic');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('easy');
            $table->integer('time_limit')->default(300)->comment('seconds per level');
            $table->integer('points_per_level')->default(10);
            $table->string('icon')->default('🧩');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('challenge_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->integer('level_number');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->json('puzzle_data');
            $table->string('answer');
            $table->json('hints')->nullable();
            $table->timestamps();
        });

        Schema::create('challenge_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('levels_completed')->default(0);
            $table->integer('time_taken')->default(0)->comment('seconds');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_attempts');
        Schema::dropIfExists('challenge_levels');
        Schema::dropIfExists('challenges');
    }
};
