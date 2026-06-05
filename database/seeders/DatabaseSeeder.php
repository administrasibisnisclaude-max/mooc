<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\TutorProfile;
use App\Models\Category;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Admin EduPlatform',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
        ]);

        // Tutor
        $tutor = User::create([
            'name' => 'Budi Santoso',
            'email' => 'tutor@demo.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
            'is_verified' => true,
            'bio' => 'Full-stack developer dengan pengalaman 10 tahun.',
        ]);
        TutorProfile::create([
            'user_id' => $tutor->id,
            'expertise' => 'Laravel, React, Node.js, Python',
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        $tutor2 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'tutor2@demo.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
            'is_verified' => true,
            'bio' => 'Data scientist dan AI researcher.',
        ]);
        TutorProfile::create([
            'user_id' => $tutor2->id,
            'expertise' => 'Python, Machine Learning, Data Science',
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);

        // Students
        $student = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'student@demo.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Dewi Putri',
            'email' => 'student2@demo.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // Pending tutor
        $pendingTutor = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'pending_tutor@demo.com',
            'password' => Hash::make('password'),
            'role' => 'tutor',
        ]);
        TutorProfile::create([
            'user_id' => $pendingTutor->id,
            'expertise' => 'Mobile Development, Flutter',
            'verification_status' => 'pending',
        ]);

        // Categories
        $categories = [
            ['name' => 'Pemrograman & Teknologi', 'icon' => '💻'],
            ['name' => 'Data Science & AI', 'icon' => '🤖'],
            ['name' => 'Desain & Kreativitas', 'icon' => '🎨'],
            ['name' => 'Bisnis & Entrepreneurship', 'icon' => '💼'],
            ['name' => 'Bahasa', 'icon' => '🌍'],
            ['name' => 'Pemasaran Digital', 'icon' => '📱'],
        ];

        $createdCategories = [];
        foreach ($categories as $cat) {
            $createdCategories[] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'description' => 'Kategori ' . $cat['name'],
            ]);
        }

        // Course 1 - Laravel
        $course1 = Course::create([
            'title' => 'Belajar Laravel 10 dari Nol hingga Expert',
            'slug' => 'belajar-laravel-10-nol-hingga-expert',
            'description' => 'Pelajari framework PHP terpopuler, Laravel 10, dari dasar hingga mahir. Cocok untuk pemula yang ingin menjadi web developer profesional.',
            'tutor_id' => $tutor->id,
            'category_id' => $createdCategories[0]->id,
            'price' => 299000,
            'level' => 'beginner',
            'status' => 'published',
            'language' => 'Indonesia',
            'what_youll_learn' => "Memahami konsep MVC\nMembuat REST API\nMengelola database dengan Eloquent\nAuthentikasi & Authorization\nDeployment ke server",
            'requirements' => "Dasar HTML/CSS\nDasar PHP\nKomputer dengan RAM minimal 4GB",
        ]);

        $section1 = Section::create(['course_id' => $course1->id, 'title' => 'Pengenalan Laravel', 'order' => 1]);
        Lesson::create(['section_id' => $section1->id, 'title' => 'Apa itu Laravel?', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'duration' => 600, 'order' => 1, 'is_free_preview' => true, 'content' => 'Pengenalan framework Laravel dan ekosistemnya.']);
        Lesson::create(['section_id' => $section1->id, 'title' => 'Instalasi Laravel', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'duration' => 900, 'order' => 2, 'content' => 'Cara menginstall Laravel menggunakan Composer.']);

        $section2 = Section::create(['course_id' => $course1->id, 'title' => 'Routing & Controller', 'order' => 2]);
        Lesson::create(['section_id' => $section2->id, 'title' => 'Memahami Routing', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'duration' => 1200, 'order' => 1, 'content' => 'Penjelasan routing di Laravel.']);
        Lesson::create(['section_id' => $section2->id, 'title' => 'Membuat Controller', 'type' => 'text', 'duration' => 0, 'order' => 2, 'content' => 'Controller adalah komponen MVC yang menangani logika bisnis aplikasi.']);

        $section3 = Section::create(['course_id' => $course1->id, 'title' => 'Database & Eloquent', 'order' => 3]);
        Lesson::create(['section_id' => $section3->id, 'title' => 'Migration Database', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY', 'duration' => 1500, 'order' => 1, 'content' => 'Cara membuat dan menjalankan migration.']);

        // Course 2 - Python
        $course2 = Course::create([
            'title' => 'Python untuk Data Science & Machine Learning',
            'slug' => 'python-data-science-machine-learning',
            'description' => 'Kuasai Python untuk analisis data, visualisasi, dan machine learning. Dilengkapi dengan proyek nyata dan dataset real-world.',
            'tutor_id' => $tutor2->id,
            'category_id' => $createdCategories[1]->id,
            'price' => 399000,
            'level' => 'intermediate',
            'status' => 'published',
            'language' => 'Indonesia',
            'what_youll_learn' => "Dasar Python\nNumPy & Pandas\nVisualisasi dengan Matplotlib\nMachine Learning dengan Scikit-learn\nDeep Learning dasar",
            'requirements' => "Dasar pemrograman apapun\nMathematika dasar",
        ]);

        $sectionPy1 = Section::create(['course_id' => $course2->id, 'title' => 'Dasar Python', 'order' => 1]);
        Lesson::create(['section_id' => $sectionPy1->id, 'title' => 'Pengenalan Python', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=kqtD5dpn9C8', 'duration' => 1200, 'order' => 1, 'is_free_preview' => true]);
        Lesson::create(['section_id' => $sectionPy1->id, 'title' => 'Tipe Data Python', 'type' => 'text', 'duration' => 0, 'order' => 2, 'content' => 'Python memiliki berbagai tipe data: int, float, str, list, dict, tuple, set.']);

        // Course 3 - Free
        $course3 = Course::create([
            'title' => 'HTML & CSS untuk Pemula',
            'slug' => 'html-css-untuk-pemula',
            'description' => 'Pelajari dasar-dasar web development dengan HTML dan CSS secara gratis. Cocok untuk yang baru memulai belajar coding.',
            'tutor_id' => $tutor->id,
            'category_id' => $createdCategories[0]->id,
            'price' => 0,
            'level' => 'beginner',
            'status' => 'published',
            'language' => 'Indonesia',
            'what_youll_learn' => "Struktur HTML\nStyling dengan CSS\nResponsive Design",
        ]);

        $sectionHtml = Section::create(['course_id' => $course3->id, 'title' => 'HTML Dasar', 'order' => 1]);
        Lesson::create(['section_id' => $sectionHtml->id, 'title' => 'Pengenalan HTML', 'type' => 'video', 'video_url' => 'https://www.youtube.com/watch?v=kUMe1FH4CHE', 'duration' => 900, 'order' => 1, 'is_free_preview' => true]);

        // Pending course
        Course::create([
            'title' => 'React.js untuk Frontend Developer',
            'slug' => 'react-js-frontend-developer',
            'description' => 'Belajar React.js library JavaScript terpopuler untuk membuat UI modern.',
            'tutor_id' => $tutor->id,
            'category_id' => $createdCategories[0]->id,
            'price' => 349000,
            'level' => 'intermediate',
            'status' => 'pending',
            'language' => 'Indonesia',
        ]);

        // Enrollments
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course1->id,
            'enrolled_at' => now()->subDays(10),
            'progress_percentage' => 33,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course3->id,
            'enrolled_at' => now()->subDays(5),
            'progress_percentage' => 100,
            'completed_at' => now()->subDays(1),
        ]);

        // Certificate
        \App\Models\Certificate::create([
            'user_id' => $student->id,
            'course_id' => $course3->id,
            'certificate_number' => 'CERT-' . strtoupper(uniqid()),
            'issued_at' => now()->subDays(1),
        ]);

        // Reviews
        \App\Models\CourseReview::create([
            'user_id' => $student->id,
            'course_id' => $course1->id,
            'rating' => 5,
            'review' => 'Kursus yang sangat bagus! Penjelasannya mudah dipahami.',
        ]);

        \App\Models\CourseReview::create([
            'user_id' => $student->id,
            'course_id' => $course3->id,
            'rating' => 4,
            'review' => 'Materi lengkap dan terstruktur dengan baik.',
        ]);

        // Forum
        $thread = \App\Models\ForumThread::create([
            'course_id' => $course1->id,
            'user_id' => $student->id,
            'title' => 'Cara debug error 500 di Laravel?',
            'content' => 'Halo, saya mengalami error 500 ketika menjalankan aplikasi. Bagaimana cara debugnya?',
        ]);

        \App\Models\ForumReply::create([
            'thread_id' => $thread->id,
            'user_id' => $tutor->id,
            'content' => 'Error 500 biasanya karena kesalahan kode PHP. Cek file log di storage/logs/laravel.log untuk detail errornya.',
            'is_answer' => true,
        ]);

        // Announcement
        \App\Models\Announcement::create([
            'course_id' => $course1->id,
            'tutor_id' => $tutor->id,
            'title' => 'Update Materi Terbaru',
            'content' => 'Kami telah menambahkan modul baru tentang Laravel Livewire. Silakan cek konten terbaru!',
        ]);

        // Quiz
        $quiz = \App\Models\Quiz::create([
            'course_id' => $course1->id,
            'section_id' => $section1->id,
            'title' => 'Quiz Pengenalan Laravel',
            'description' => 'Uji pemahaman Anda tentang dasar-dasar Laravel.',
            'passing_score' => 70,
            'time_limit' => 15,
        ]);

        $q1 = \App\Models\QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Apa kepanjangan dari MVC?', 'type' => 'multiple_choice', 'order' => 1]);
        \App\Models\QuizOption::create(['question_id' => $q1->id, 'option_text' => 'Model View Controller', 'is_correct' => true]);
        \App\Models\QuizOption::create(['question_id' => $q1->id, 'option_text' => 'Master Visual Code', 'is_correct' => false]);
        \App\Models\QuizOption::create(['question_id' => $q1->id, 'option_text' => 'Module View Component', 'is_correct' => false]);

        $q2 = \App\Models\QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Laravel menggunakan bahasa pemrograman PHP.', 'type' => 'true_false', 'order' => 2]);
        \App\Models\QuizOption::create(['question_id' => $q2->id, 'option_text' => 'Benar', 'is_correct' => true]);
        \App\Models\QuizOption::create(['question_id' => $q2->id, 'option_text' => 'Salah', 'is_correct' => false]);

        // Assignment
        $assignment = \App\Models\Assignment::create([
            'course_id' => $course1->id,
            'section_id' => $section2->id,
            'title' => 'Buat Aplikasi CRUD Sederhana',
            'description' => "Buat aplikasi CRUD (Create, Read, Update, Delete) menggunakan Laravel dengan ketentuan:\n1. Tabel produk dengan field: nama, harga, stok\n2. Halaman index menampilkan semua produk\n3. Form tambah dan edit produk\n4. Fungsi hapus dengan konfirmasi",
            'due_date' => now()->addDays(14),
            'max_score' => 100,
            'file_requirements' => 'Upload file ZIP berisi project Laravel Anda. Maks 50MB.',
        ]);
    }
}
