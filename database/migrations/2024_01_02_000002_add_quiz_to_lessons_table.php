<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend lessons.type enum to include 'quiz'
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video','document','text','quiz') DEFAULT 'text'");

        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('quiz_id')->nullable()->after('type');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['quiz_id']);
            $table->dropColumn('quiz_id');
        });
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video','document','text') DEFAULT 'text'");
    }
};
