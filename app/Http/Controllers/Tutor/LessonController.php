<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function createSection(Request $request, Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate(['title' => 'required|string|max:255']);
        $order = $course->sections()->max('order') + 1;
        $course->sections()->create(['title' => $request->title, 'order' => $order]);
        return back()->with('success', 'Bagian berhasil ditambahkan.');
    }

    public function destroySection(Section $section)
    {
        abort_if($section->course->tutor_id !== Auth::id(), 403);
        $section->delete();
        return back()->with('success', 'Bagian berhasil dihapus.');
    }

    public function create(Section $section)
    {
        abort_if($section->course->tutor_id !== Auth::id(), 403);
        return view('tutor.lessons.create', compact('section'));
    }

    public function store(Request $request, Section $section)
    {
        abort_if($section->course->tutor_id !== Auth::id(), 403);

        $request->validate([
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:video,document,text,quiz',
            'content'        => 'nullable|string',
            'video_url'      => 'nullable|string',
            'duration'       => 'nullable|integer|min:0',
            'is_free_preview'=> 'nullable|boolean',
            // quiz-specific
            'passing_score'  => 'required_if:type,quiz|nullable|integer|min:1|max:100',
            'time_limit'     => 'nullable|integer|min:1',
            'questions'      => 'required_if:type,quiz|nullable|array|min:1',
            'questions.*.question'      => 'required_if:type,quiz|string',
            'questions.*.correct'       => 'required_if:type,quiz|integer',
            'questions.*.options'       => 'required_if:type,quiz|array|min:2',
            'questions.*.options.*'     => 'required_if:type,quiz|string',
        ]);

        DB::transaction(function () use ($request, $section) {
            $order = $section->lessons()->max('order') + 1;

            $lesson = $section->lessons()->create([
                'title'          => $request->title,
                'type'           => $request->type,
                'content'        => $request->type !== 'quiz' ? $request->content : null,
                'video_url'      => $request->video_url,
                'duration'       => $request->duration ?? 0,
                'order'          => $order,
                'is_free_preview'=> $request->boolean('is_free_preview'),
            ]);

            if ($request->type === 'quiz') {
                $quiz = Quiz::create([
                    'course_id'    => $section->course_id,
                    'section_id'   => $section->id,
                    'title'        => $request->title,
                    'description'  => $request->content,
                    'passing_score'=> $request->passing_score ?? 70,
                    'time_limit'   => $request->time_limit,
                ]);

                $this->saveQuizQuestions($quiz, $request->questions ?? []);

                $lesson->update(['quiz_id' => $quiz->id]);
            }
        });

        return redirect()->route('tutor.courses.show', $section->course_id)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Lesson $lesson)
    {
        abort_if($lesson->section->course->tutor_id !== Auth::id(), 403);
        $lesson->load('quiz.questions.options');
        return view('tutor.lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        abort_if($lesson->section->course->tutor_id !== Auth::id(), 403);

        $request->validate([
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:video,document,text,quiz',
            'passing_score'  => 'required_if:type,quiz|nullable|integer|min:1|max:100',
            'time_limit'     => 'nullable|integer|min:1',
            'questions'      => 'nullable|array',
            'questions.*.question'  => 'required_with:questions|string',
            'questions.*.correct'   => 'required_with:questions|integer',
            'questions.*.options'   => 'required_with:questions|array|min:2',
        ]);

        DB::transaction(function () use ($request, $lesson) {
            $lesson->update([
                'title'          => $request->title,
                'type'           => $request->type,
                'content'        => $request->type !== 'quiz' ? $request->content : null,
                'video_url'      => $request->video_url,
                'duration'       => $request->duration ?? $lesson->duration,
                'is_free_preview'=> $request->boolean('is_free_preview'),
            ]);

            if ($request->type === 'quiz') {
                if ($lesson->quiz_id && $lesson->quiz) {
                    // Update existing quiz
                    $quiz = $lesson->quiz;
                    $quiz->update([
                        'title'        => $request->title,
                        'description'  => $request->content,
                        'passing_score'=> $request->passing_score ?? 70,
                        'time_limit'   => $request->time_limit,
                    ]);
                    // Replace questions
                    $quiz->questions()->delete();
                    $this->saveQuizQuestions($quiz, $request->questions ?? []);
                } else {
                    // Create new quiz for this lesson
                    $quiz = Quiz::create([
                        'course_id'    => $lesson->section->course_id,
                        'section_id'   => $lesson->section_id,
                        'title'        => $request->title,
                        'description'  => $request->content,
                        'passing_score'=> $request->passing_score ?? 70,
                        'time_limit'   => $request->time_limit,
                    ]);
                    $this->saveQuizQuestions($quiz, $request->questions ?? []);
                    $lesson->update(['quiz_id' => $quiz->id]);
                }
            }
        });

        return redirect()->route('tutor.courses.show', $lesson->section->course_id)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Lesson $lesson)
    {
        abort_if($lesson->section->course->tutor_id !== Auth::id(), 403);
        $courseId = $lesson->section->course_id;
        DB::transaction(function () use ($lesson) {
            if ($lesson->quiz_id) {
                $lesson->quiz?->delete();
            }
            $lesson->delete();
        });
        return redirect()->route('tutor.courses.show', $courseId)->with('success', 'Materi berhasil dihapus.');
    }

    private function saveQuizQuestions(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $i => $q) {
            if (empty($q['question'])) continue;

            $question = $quiz->questions()->create([
                'question' => $q['question'],
                'type'     => 'multiple_choice',
                'order'    => $i + 1,
            ]);

            $correctIdx = (int) ($q['correct'] ?? 0);
            foreach ($q['options'] as $j => $optionText) {
                if (empty($optionText)) continue;
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct'  => ($j === $correctIdx),
                ]);
            }
        }
    }
}
