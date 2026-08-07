<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // MySQL supports MODIFY COLUMN for ENUM; SQLite uses TEXT and ignores ENUM constraints
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video','document','text','quiz') DEFAULT 'text'");
        }
        // SQLite: column is already TEXT — no change needed, 'quiz' value will be accepted

        Schema::table('lessons', function (Blueprint $table) use ($driver) {
            $table->unsignedBigInteger('quiz_id')->nullable()->after('type');

            // SQLite has limited FK support; only add constraint on MySQL/pgsql
            if ($driver !== 'sqlite') {
                $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        Schema::table('lessons', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->dropForeign(['quiz_id']);
            }
            $table->dropColumn('quiz_id');
        });

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video','document','text') DEFAULT 'text'");
        }
    }
};
