<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Tutor;
use App\Http\Controllers\Student;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [HomeController::class, 'courses'])->name('courses.index');
Route::get('/courses/{slug}', [HomeController::class, 'courseDetail'])->name('courses.detail');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect dashboard
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard')->middleware('auth');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', Admin\UserController::class);
    Route::post('users/{user}/toggle-verify', [Admin\UserController::class, 'toggleVerify'])->name('users.toggle-verify');

    // Tutor Verification
    Route::get('tutors', [Admin\TutorVerificationController::class, 'index'])->name('tutors.index');
    Route::get('tutors/{user}', [Admin\TutorVerificationController::class, 'show'])->name('tutors.show');
    Route::post('tutors/{user}/approve', [Admin\TutorVerificationController::class, 'approve'])->name('tutors.approve');
    Route::post('tutors/{user}/reject', [Admin\TutorVerificationController::class, 'reject'])->name('tutors.reject');

    // Course Verification
    Route::get('courses', [Admin\CourseVerificationController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [Admin\CourseVerificationController::class, 'show'])->name('courses.show');
    Route::post('courses/{course}/approve', [Admin\CourseVerificationController::class, 'approve'])->name('courses.approve');
    Route::post('courses/{course}/reject', [Admin\CourseVerificationController::class, 'reject'])->name('courses.reject');

    // Categories
    Route::resource('categories', Admin\CategoryController::class);

    // Certificates
    Route::get('certificates', [Admin\CertificateController::class, 'index'])->name('certificates.index');
    Route::post('certificates/issue', [Admin\CertificateController::class, 'issue'])->name('certificates.issue');
    Route::delete('certificates/{certificate}', [Admin\CertificateController::class, 'destroy'])->name('certificates.destroy');

    // Reports
    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
});

// Tutor Routes
Route::prefix('tutor')->name('tutor.')->middleware(['auth', 'role:tutor'])->group(function () {
    Route::get('/', [Tutor\DashboardController::class, 'index'])->name('dashboard');

    // Courses
    Route::resource('courses', Tutor\CourseController::class);
    Route::post('courses/{course}/submit', [Tutor\CourseController::class, 'submitForReview'])->name('courses.submit');

    // Sections & Lessons
    Route::post('courses/{course}/sections', [Tutor\LessonController::class, 'createSection'])->name('sections.store');
    Route::delete('sections/{section}', [Tutor\LessonController::class, 'destroySection'])->name('sections.destroy');
    Route::get('sections/{section}/lessons/create', [Tutor\LessonController::class, 'create'])->name('lessons.create');
    Route::post('sections/{section}/lessons', [Tutor\LessonController::class, 'store'])->name('lessons.store');
    Route::get('lessons/{lesson}/edit', [Tutor\LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [Tutor\LessonController::class, 'update'])->name('lessons.update');
    Route::delete('lessons/{lesson}', [Tutor\LessonController::class, 'destroy'])->name('lessons.destroy');

    // Quizzes
    Route::get('courses/{course}/quizzes', [Tutor\QuizController::class, 'index'])->name('quizzes.index');
    Route::get('courses/{course}/quizzes/create', [Tutor\QuizController::class, 'create'])->name('quizzes.create');
    Route::post('courses/{course}/quizzes', [Tutor\QuizController::class, 'store'])->name('quizzes.store');
    Route::get('courses/{course}/quizzes/{quiz}/edit', [Tutor\QuizController::class, 'edit'])->name('quizzes.edit');
    Route::post('courses/{course}/quizzes/{quiz}/questions', [Tutor\QuizController::class, 'addQuestion'])->name('quizzes.add-question');
    Route::delete('questions/{question}', [Tutor\QuizController::class, 'destroyQuestion'])->name('questions.destroy');
    Route::delete('courses/{course}/quizzes/{quiz}', [Tutor\QuizController::class, 'destroy'])->name('quizzes.destroy');

    // Assignments
    Route::get('courses/{course}/assignments', [Tutor\AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('courses/{course}/assignments/create', [Tutor\AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('courses/{course}/assignments', [Tutor\AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('courses/{course}/assignments/{assignment}/edit', [Tutor\AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('courses/{course}/assignments/{assignment}', [Tutor\AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('courses/{course}/assignments/{assignment}', [Tutor\AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Grading
    Route::get('courses/{course}/assignments/{assignment}/submissions', [Tutor\GradingController::class, 'index'])->name('grading.index');
    Route::post('submissions/{submission}/grade', [Tutor\GradingController::class, 'grade'])->name('grading.grade');

    // Student Monitoring
    Route::get('courses/{course}/students', [Tutor\StudentMonitoringController::class, 'index'])->name('students.index');
    Route::get('courses/{course}/students/{userId}', [Tutor\StudentMonitoringController::class, 'show'])->name('students.show');

    // Announcements
    Route::get('courses/{course}/announcements', [Tutor\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('courses/{course}/announcements', [Tutor\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('courses/{course}/announcements/{announcement}', [Tutor\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

// Student Routes
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/dashboard', [Student\DashboardController::class, 'index'])->name('student.dashboard');
    Route::get('/student/courses', [Student\EnrollmentController::class, 'myCourses'])->name('student.courses');
    Route::post('/courses/{course}/enroll', [Student\EnrollmentController::class, 'enroll'])->name('student.enroll');
    Route::get('/learn/{course}', [Student\EnrollmentController::class, 'learn'])->name('student.learn');

    // Lessons
    Route::get('/lessons/{lesson}', [Student\LessonController::class, 'show'])->name('student.lesson');
    Route::post('/lessons/{lesson}/complete', [Student\LessonController::class, 'markComplete'])->name('student.lesson.complete');

    // Quizzes
    Route::get('/quizzes/{quiz}/start', [Student\QuizAttemptController::class, 'start'])->name('student.quiz.start');
    Route::post('/quiz-attempts/{attempt}/submit', [Student\QuizAttemptController::class, 'submit'])->name('student.quiz.submit');
    Route::get('/quiz-attempts/{attempt}/result', [Student\QuizAttemptController::class, 'result'])->name('student.quiz.result');

    // Assignments
    Route::get('/assignments/{assignment}', [Student\AssignmentSubmissionController::class, 'show'])->name('student.assignment');
    Route::post('/assignments/{assignment}/submit', [Student\AssignmentSubmissionController::class, 'submit'])->name('student.assignment.submit');

    // Forum
    Route::get('/learn/{course}/forum', [Student\ForumController::class, 'index'])->name('student.forum.index');
    Route::post('/learn/{course}/forum', [Student\ForumController::class, 'store'])->name('student.forum.store');
    Route::get('/learn/{course}/forum/{thread}', [Student\ForumController::class, 'show'])->name('student.forum.show');
    Route::post('/learn/{course}/forum/{thread}/reply', [Student\ForumController::class, 'reply'])->name('student.forum.reply');
    Route::post('/forum-replies/{reply}/mark-answer', [Student\ForumController::class, 'markAnswer'])->name('student.forum.mark-answer');

    // Certificates
    Route::get('/certificates', [Student\CertificateController::class, 'index'])->name('student.certificates');
    Route::get('/certificates/{certificate}', [Student\CertificateController::class, 'show'])->name('student.certificate.show');
});
