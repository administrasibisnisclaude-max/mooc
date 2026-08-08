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

        if ($driver === 'sqlite') {
            // SQLite cannot ALTER CHECK constraints — must recreate the table
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement("
                CREATE TABLE lessons_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    section_id INTEGER NOT NULL,
                    title VARCHAR NOT NULL,
                    type VARCHAR CHECK(type IN ('video','document','text','quiz')) DEFAULT 'text',
                    quiz_id INTEGER NULL,
                    content TEXT NULL,
                    video_url VARCHAR NULL,
                    duration INTEGER DEFAULT 0,
                    \"order\" INTEGER DEFAULT 0,
                    is_free_preview INTEGER DEFAULT 0,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
                )
            ");

            DB::statement('INSERT INTO lessons_new
                SELECT id, section_id, title, type, NULL, content, video_url, duration, "order", is_free_preview, created_at, updated_at
                FROM lessons');

            DB::statement('DROP TABLE lessons');
            DB::statement('ALTER TABLE lessons_new RENAME TO lessons');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            // MySQL / PostgreSQL
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video','document','text','quiz') DEFAULT 'text'");
            }
            Schema::table('lessons', function (Blueprint $table) {
                $table->unsignedBigInteger('quiz_id')->nullable()->after('type');
                $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');

            DB::statement("
                CREATE TABLE lessons_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    section_id INTEGER NOT NULL,
                    title VARCHAR NOT NULL,
                    type VARCHAR CHECK(type IN ('video','document','text')) DEFAULT 'text',
                    content TEXT NULL,
                    video_url VARCHAR NULL,
                    duration INTEGER DEFAULT 0,
                    \"order\" INTEGER DEFAULT 0,
                    is_free_preview INTEGER DEFAULT 0,
                    created_at DATETIME NULL,
                    updated_at DATETIME NULL,
                    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
                )
            ");

            DB::statement("INSERT INTO lessons_new
                SELECT id, section_id, title,
                    CASE WHEN type = 'quiz' THEN 'text' ELSE type END,
                    content, video_url, duration, \"order\", is_free_preview, created_at, updated_at
                FROM lessons");

            DB::statement('DROP TABLE lessons');
            DB::statement('ALTER TABLE lessons_new RENAME TO lessons');

            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropForeign(['quiz_id']);
                $table->dropColumn('quiz_id');
            });
            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video','document','text') DEFAULT 'text'");
            }
        }
    }
};
