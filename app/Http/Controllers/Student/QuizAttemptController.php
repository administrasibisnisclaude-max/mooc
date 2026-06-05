<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizAttemptController extends Controller
{
    public function start(Quiz $quiz)
    {
        $user = Auth::user();
        Enrollment::where('user_id', $user->id)->where('course_id', $quiz->course_id)->firstOrFail();

        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'score' => 0,
        ]);

        $quiz->load('questions.options');
        return view('student.quiz', compact('quiz', 'attempt'));
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        abort_if($attempt->user_id !== Auth::id(), 403);
        abort_if($attempt->submitted_at !== null, 400);

        $quiz = $attempt->quiz;
        $quiz->load('questions.options');

        $answers = $request->input('answers', []);
        $correct = 0;

        foreach ($quiz->questions as $question) {
            $optionId = $answers[$question->id] ?? null;
            QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $optionId,
            ]);

            if ($optionId) {
                $option = $question->options->find($optionId);
                if ($option && $option->is_correct) $correct++;
            }
        }

        $score = $quiz->questions->count() > 0 ? ($correct / $quiz->questions->count()) * 100 : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt->update([
            'score' => $score,
            'passed' => $passed,
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.quiz.result', $attempt)->with('success', 'Quiz berhasil dikumpulkan!');
    }

    public function result(QuizAttempt $attempt)
    {
        abort_if($attempt->user_id !== Auth::id(), 403);
        $attempt->load('quiz', 'answers.option', 'answers.question.options');
        return view('student.quiz-result', compact('attempt'));
    }
}
