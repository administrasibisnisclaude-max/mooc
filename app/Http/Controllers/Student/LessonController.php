<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function show(Lesson $lesson)
    {
        $user   = Auth::user();
        $course = $lesson->section->course;

        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();
        $lesson->load(['materials', 'quiz.questions.options']);

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['watch_time' => 0]
        );

        [$prevLesson, $nextLesson] = $this->adjacentLessons($course, $lesson);

        $completedLessonIds = LessonProgress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereHas('lesson', fn($q) => $q->whereHas('section', fn($q2) => $q2->where('course_id', $course->id)))
            ->pluck('lesson_id')
            ->toArray();

        // Last quiz attempt for inline quiz
        $lastAttempt = null;
        if ($lesson->type === 'quiz' && $lesson->quiz) {
            $lastAttempt = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $lesson->quiz->id)
                ->whereNotNull('submitted_at')
                ->latest()
                ->first()?->load('answers.option', 'answers.question.options');
        }

        return view('student.lesson', compact(
            'lesson', 'course', 'enrollment', 'progress',
            'prevLesson', 'nextLesson', 'completedLessonIds', 'lastAttempt'
        ));
    }

    public function markComplete(Lesson $lesson, Request $request)
    {
        $user   = Auth::user();
        $course = $lesson->section->course;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();

        LessonProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        $this->recalcProgress($user->id, $course);

        if ($request->wantsJson()) {
            [, $nextLesson] = $this->adjacentLessons($course, $lesson);
            return response()->json([
                'success'  => true,
                'next_url' => $nextLesson ? route('student.lesson', $nextLesson) : null,
            ]);
        }

        return back()->with('success', 'Pelajaran ditandai selesai!');
    }

    public function submitInlineQuiz(Lesson $lesson, Request $request)
    {
        $user   = Auth::user();
        $course = $lesson->section->course;
        Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->firstOrFail();

        abort_if(!$lesson->quiz, 404);

        $quiz = $lesson->quiz->load('questions.options');

        // Create attempt
        $attempt = QuizAttempt::create([
            'user_id'    => $user->id,
            'quiz_id'    => $quiz->id,
            'started_at' => now(),
            'score'      => 0,
        ]);

        $answers = $request->input('answers', []);
        $correct = 0;

        foreach ($quiz->questions as $question) {
            $optionId = $answers[$question->id] ?? null;
            QuizAnswer::create([
                'attempt_id'  => $attempt->id,
                'question_id' => $question->id,
                'option_id'   => $optionId,
            ]);
            if ($optionId) {
                $opt = $question->options->find($optionId);
                if ($opt && $opt->is_correct) $correct++;
            }
        }

        $total  = $quiz->questions->count();
        $score  = $total > 0 ? ($correct / $total) * 100 : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt->update([
            'score'        => $score,
            'passed'       => $passed,
            'submitted_at' => now(),
        ]);

        // Auto-complete lesson if passed
        if ($passed) {
            LessonProgress::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['completed_at' => now()]
            );
            $this->recalcProgress($user->id, $course);
        }

        [, $nextLesson] = $this->adjacentLessons($course, $lesson);

        return back()->with('quiz_result', [
            'passed'      => $passed,
            'score'       => round($score, 1),
            'correct'     => $correct,
            'total'       => $total,
            'passing'     => $quiz->passing_score,
            'next_url'    => $nextLesson ? route('student.lesson', $nextLesson) : null,
            'attempt_id'  => $attempt->id,
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function adjacentLessons($course, Lesson $lesson): array
    {
        $all  = $course->sections->flatMap->lessons->sortBy(fn($l) => [$l->section->order, $l->order])->values();
        $idx  = $all->search(fn($l) => $l->id === $lesson->id);
        $prev = ($idx > 0) ? $all[$idx - 1] : null;
        $next = ($idx !== false && $idx < $all->count() - 1) ? $all[$idx + 1] : null;
        return [$prev, $next];
    }

    private function recalcProgress(int $userId, $course): void
    {
        $total     = $course->sections->flatMap->lessons->count();
        $completed = LessonProgress::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereHas('lesson', fn($q) => $q->whereHas('section', fn($q2) => $q2->where('course_id', $course->id)))
            ->count();

        $pct        = $total > 0 ? ($completed / $total) * 100 : 0;
        $enrollment = Enrollment::where('user_id', $userId)->where('course_id', $course->id)->first();
        $enrollment?->update([
            'progress_percentage' => $pct,
            'completed_at'        => $pct >= 100 ? now() : null,
        ]);
    }
}
