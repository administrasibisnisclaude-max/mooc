<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeLevel;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    public function index(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        $challenges = $course->challenges()->withCount('levels')->get();
        return view('tutor.challenges.index', compact('course', 'challenges'));
    }

    public function create(Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        return view('tutor.challenges.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);

        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'required|in:problem_solving,puzzle_logic',
            'difficulty'       => 'required|in:easy,medium,hard',
            'time_limit'       => 'required|integer|min:1|max:180',
            'points_per_level' => 'required|integer|min:1|max:100',
            'icon'             => 'nullable|string|max:10',
            'levels'           => 'required|array|min:1',
            'levels.*.title'         => 'required|string|max:255',
            'levels.*.instructions'  => 'required|string',
            'levels.*.answer'        => 'required|string|max:500',
            'levels.*.puzzle_type'   => 'required|string',
            'levels.*.options'       => 'required|array|min:2',
            'levels.*.answer_index'  => 'required|integer|min:0',
            'levels.*.explanation'   => 'nullable|string',
            'levels.*.hints'         => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $course) {
            $challenge = Challenge::create([
                'course_id'        => $course->id,
                'tutor_id'         => Auth::id(),
                'title'            => $request->title,
                'description'      => $request->description,
                'type'             => $request->type,
                'difficulty'       => $request->difficulty,
                'time_limit'       => $request->time_limit,
                'points_per_level' => $request->points_per_level,
                'icon'             => $request->icon ?: '🎯',
                'is_active'        => true,
            ]);

            foreach ($request->levels as $i => $lvl) {
                $puzzleData = [
                    'type'         => $lvl['puzzle_type'],
                    'options'      => $lvl['options'],
                    'answer_index' => (int) $lvl['answer_index'],
                    'explanation'  => $lvl['explanation'] ?? '',
                ];

                // Type-specific extra fields
                if ($lvl['puzzle_type'] === 'multiple_choice') {
                    $puzzleData['question'] = $lvl['question'] ?? '';
                } elseif ($lvl['puzzle_type'] === 'code_output') {
                    $puzzleData['code']     = $lvl['code'] ?? '';
                    $puzzleData['language'] = $lvl['language'] ?? 'python';
                } elseif ($lvl['puzzle_type'] === 'code_debug') {
                    $puzzleData['code']     = $lvl['code'] ?? '';
                    $puzzleData['language'] = $lvl['language'] ?? 'python';
                    $puzzleData['bug']      = $lvl['bug'] ?? '';
                } elseif ($lvl['puzzle_type'] === 'riddle') {
                    $puzzleData['riddle'] = $lvl['riddle'] ?? '';
                } elseif ($lvl['puzzle_type'] === 'math') {
                    $puzzleData['expression'] = $lvl['expression'] ?? '';
                } elseif ($lvl['puzzle_type'] === 'number_sequence') {
                    $rawSeq = array_map(fn($v) => $v === '' ? null : (int)$v, explode(',', $lvl['sequence'] ?? ''));
                    $puzzleData['sequence']    = $rawSeq;
                    $puzzleData['blank_index'] = (int) ($lvl['blank_index'] ?? 0);
                    $puzzleData['pattern']     = $lvl['pattern'] ?? '';
                } elseif ($lvl['puzzle_type'] === 'anagram') {
                    $puzzleData['scrambled']   = $lvl['scrambled'] ?? '';
                    $puzzleData['answer_word'] = $lvl['answer_word'] ?? '';
                }

                $hints = array_filter(array_map('trim', $lvl['hints'] ?? []), fn($h) => $h !== '');

                ChallengeLevel::create([
                    'challenge_id' => $challenge->id,
                    'level_number' => $i + 1,
                    'title'        => $lvl['title'],
                    'instructions' => $lvl['instructions'],
                    'puzzle_data'  => $puzzleData,
                    'answer'       => $lvl['options'][(int)$lvl['answer_index']] ?? $lvl['answer'],
                    'hints'        => array_values($hints),
                ]);
            }
        });

        return redirect()->route('tutor.challenges.index', $course)
            ->with('success', 'Challenge berhasil dibuat!');
    }

    public function edit(Course $course, Challenge $challenge)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        abort_if($challenge->course_id !== $course->id, 404);
        $challenge->load('levels');
        return view('tutor.challenges.edit', compact('course', 'challenge'));
    }

    public function update(Request $request, Course $course, Challenge $challenge)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        abort_if($challenge->course_id !== $course->id, 404);

        $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'difficulty'       => 'required|in:easy,medium,hard',
            'time_limit'       => 'required|integer|min:1|max:180',
            'points_per_level' => 'required|integer|min:1|max:100',
            'is_active'        => 'boolean',
        ]);

        $challenge->update($request->only(['title', 'description', 'difficulty', 'time_limit', 'points_per_level', 'icon']) + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('tutor.challenges.index', $course)
            ->with('success', 'Challenge berhasil diperbarui!');
    }

    public function destroy(Course $course, Challenge $challenge)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        abort_if($challenge->course_id !== $course->id, 404);
        $challenge->delete();
        return back()->with('success', 'Challenge dihapus.');
    }
}
