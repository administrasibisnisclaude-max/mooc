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
        $challenges = $course->challenges()
            ->withCount('levels')
            ->withCount('attempts')
            ->get();
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
            'levels.*.title'        => 'required|string|max:255',
            'levels.*.instructions' => 'required|string',
            'levels.*.puzzle_type'  => 'required|string',
            'levels.*.options'      => 'required|array|min:2',
            'levels.*.answer_index' => 'required|integer|min:0',
            'levels.*.explanation'  => 'nullable|string',
            'levels.*.hints'        => 'nullable|array',
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
                $this->createLevel($challenge->id, $i + 1, $lvl);
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
        $attemptsCount = $challenge->attempts()->count();
        $uniquePlayers = $challenge->attempts()->distinct('user_id')->count('user_id');
        return view('tutor.challenges.edit', compact('course', 'challenge', 'attemptsCount', 'uniquePlayers'));
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
            'icon'             => 'nullable|string|max:10',
            'is_active'        => 'boolean',
        ]);

        $challenge->update($request->only(['title', 'description', 'difficulty', 'time_limit', 'points_per_level', 'icon']) + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('tutor.challenges.edit', [$course, $challenge])
            ->with('success', 'Info challenge berhasil diperbarui!');
    }

    public function destroy(Course $course, Challenge $challenge)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        abort_if($challenge->course_id !== $course->id, 404);
        $challenge->delete();
        return redirect()->route('tutor.challenges.index', $course)
            ->with('success', 'Challenge berhasil dihapus.');
    }

    // ── Level Management ──────────────────────────────────────────────

    public function storeLevel(Request $request, Course $course, Challenge $challenge)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        abort_if($challenge->course_id !== $course->id, 404);

        $request->validate([
            'title'        => 'required|string|max:255',
            'instructions' => 'required|string',
            'puzzle_type'  => 'required|string',
            'options'      => 'required|array|min:2',
            'answer_index' => 'required|integer|min:0',
            'explanation'  => 'nullable|string',
            'hints'        => 'nullable|array',
        ]);

        $nextNumber = $challenge->levels()->max('level_number') + 1;
        $this->createLevel($challenge->id, $nextNumber, $request->all());

        return redirect()->route('tutor.challenges.edit', [$course, $challenge])
            ->with('success', "Level {$nextNumber} berhasil ditambahkan!");
    }

    public function destroyLevel(Course $course, Challenge $challenge, ChallengeLevel $level)
    {
        abort_if($course->tutor_id !== Auth::id(), 403);
        abort_if($challenge->course_id !== $course->id, 404);
        abort_if($level->challenge_id !== $challenge->id, 404);

        $level->delete();

        // Re-number remaining levels
        $challenge->levels()->orderBy('level_number')->each(function ($l, $i) {
            $l->update(['level_number' => $i + 1]);
        });

        return redirect()->route('tutor.challenges.edit', [$course, $challenge])
            ->with('success', 'Level berhasil dihapus dan urutan diperbarui.');
    }

    // ── Private Helper ────────────────────────────────────────────────

    private function createLevel(int $challengeId, int $levelNumber, array $lvl): void
    {
        $puzzleData = [
            'type'         => $lvl['puzzle_type'],
            'options'      => $lvl['options'],
            'answer_index' => (int) $lvl['answer_index'],
            'explanation'  => $lvl['explanation'] ?? '',
        ];

        match ($lvl['puzzle_type']) {
            'multiple_choice'  => $puzzleData['question'] = $lvl['question'] ?? '',
            'code_output'      => [$puzzleData['code'] = $lvl['code'] ?? '', $puzzleData['language'] = $lvl['language'] ?? 'python'],
            'code_debug'       => [$puzzleData['code'] = $lvl['code'] ?? '', $puzzleData['language'] = $lvl['language'] ?? 'python', $puzzleData['bug'] = $lvl['bug'] ?? ''],
            'riddle'           => $puzzleData['riddle'] = $lvl['riddle'] ?? '',
            'math'             => $puzzleData['expression'] = $lvl['expression'] ?? '',
            'number_sequence'  => [
                $puzzleData['sequence']    = array_map(fn($v) => $v === '' ? null : (int)$v, explode(',', $lvl['sequence'] ?? '')),
                $puzzleData['blank_index'] = (int) ($lvl['blank_index'] ?? 0),
                $puzzleData['pattern']     = $lvl['pattern'] ?? '',
            ],
            'anagram'          => [$puzzleData['scrambled'] = $lvl['scrambled'] ?? '', $puzzleData['answer_word'] = $lvl['answer_word'] ?? ''],
            default            => null,
        };

        $hints = array_values(array_filter(array_map('trim', $lvl['hints'] ?? []), fn($h) => $h !== ''));

        ChallengeLevel::create([
            'challenge_id' => $challengeId,
            'level_number' => $levelNumber,
            'title'        => $lvl['title'],
            'instructions' => $lvl['instructions'],
            'puzzle_data'  => $puzzleData,
            'answer'       => $lvl['options'][(int)$lvl['answer_index']] ?? ($lvl['answer'] ?? ''),
            'hints'        => $hints,
        ]);
    }
}
