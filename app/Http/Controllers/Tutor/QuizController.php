<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $quizzes = $course->quizzes()->with('questions')->get();
        return view('tutor.quizzes.index', compact('course', 'quizzes'));
    }

    public function create(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $sections = $course->sections;
        return view('tutor.quizzes.create', compact('course', 'sections'));
    }

    public function store(Request $request, Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate([
            'title' => 'required|string|max:255',
            'passing_score' => 'required|integer|min:1|max:100',
            'time_limit' => 'nullable|integer|min:1',
        ]);

        $quiz = $course->quizzes()->create($request->only('title', 'description', 'passing_score', 'time_limit', 'section_id'));
        return redirect()->route('tutor.quizzes.edit', [$course, $quiz])->with('success', 'Quiz berhasil dibuat. Tambahkan pertanyaan.');
    }

    public function edit(Course $course, Quiz $quiz)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $quiz->load('questions.options');
        return view('tutor.quizzes.edit', compact('course', 'quiz'));
    }

    public function addQuestion(Request $request, Course $course, Quiz $quiz)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $request->validate([
            'question' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false',
            'options' => 'required|array|min:2',
            'correct_option' => 'required|integer',
        ]);

        $order = $quiz->questions()->max('order') + 1;
        $question = $quiz->questions()->create([
            'question' => $request->question,
            'type' => $request->type,
            'order' => $order,
        ]);

        foreach ($request->options as $i => $optionText) {
            if (!empty($optionText)) {
                $question->options()->create([
                    'option_text' => $optionText,
                    'is_correct' => ($i == $request->correct_option),
                ]);
            }
        }

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function destroyQuestion(QuizQuestion $question)
    {
        abort_if($question->quiz->course->tutor_id !== Auth::id(), 403);
        $question->delete();
        return back()->with('success', 'Pertanyaan dihapus.');
    }

    public function destroy(Course $course, Quiz $quiz)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $quiz->delete();
        return redirect()->route('tutor.quizzes.index', $course)->with('success', 'Quiz berhasil dihapus.');
    }
}
