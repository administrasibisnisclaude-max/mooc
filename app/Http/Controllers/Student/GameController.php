<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $challenges = Challenge::where('is_active', true)
            ->with(['levels', 'attempts' => fn($q) => $q->where('user_id', $user->id)->orderByDesc('score')->limit(1)])
            ->get();

        // Leaderboard: top 10 players by total score
        $leaderboard = ChallengeAttempt::with('user')
            ->selectRaw('user_id, SUM(score) as total_score, COUNT(DISTINCT challenge_id) as challenges_played')
            ->where('is_completed', true)
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit(10)
            ->get();

        return view('student.games.index', compact('challenges', 'leaderboard'));
    }

    public function show(Challenge $challenge)
    {
        abort_if(!$challenge->is_active, 404);
        $challenge->load('levels');
        $user       = Auth::user();
        $bestAttempt = ChallengeAttempt::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->orderByDesc('score')
            ->first();

        return view('student.games.show', compact('challenge', 'bestAttempt'));
    }

    // AJAX: check a single level answer
    public function checkAnswer(Request $request, Challenge $challenge)
    {
        $request->validate([
            'level_number' => 'required|integer|min:1',
            'answer'       => 'required|string',
            'time_taken'   => 'required|integer|min:0',
        ]);

        $level = $challenge->levels()->where('level_number', $request->level_number)->firstOrFail();

        $correct = strtolower(trim($request->answer)) === strtolower(trim($level->answer));

        return response()->json([
            'correct'      => $correct,
            'correct_answer' => $correct ? null : $level->answer,
            'explanation'  => $level->puzzle_data['explanation'] ?? null,
            'hints'        => $level->hints ?? [],
        ]);
    }

    // Save completed attempt
    public function saveAttempt(Request $request, Challenge $challenge)
    {
        $request->validate([
            'score'            => 'required|integer|min:0',
            'levels_completed' => 'required|integer|min:0',
            'time_taken'       => 'required|integer|min:0',
            'is_completed'     => 'required|boolean',
        ]);

        $user = Auth::user();
        $totalLevels = $challenge->levels()->count();

        $attempt = ChallengeAttempt::create([
            'user_id'          => $user->id,
            'challenge_id'     => $challenge->id,
            'score'            => $request->score,
            'levels_completed' => $request->levels_completed,
            'time_taken'       => $request->time_taken,
            'is_completed'     => $request->is_completed,
            'started_at'       => now()->subSeconds($request->time_taken),
            'completed_at'     => $request->is_completed ? now() : null,
        ]);

        // Personal best
        $best = ChallengeAttempt::where('user_id', $user->id)
            ->where('challenge_id', $challenge->id)
            ->max('score');

        return response()->json([
            'saved'      => true,
            'attempt_id' => $attempt->id,
            'is_best'    => $attempt->score >= $best,
            'total_score' => ChallengeAttempt::where('user_id', $user->id)->sum('score'),
        ]);
    }

    public function play(Challenge $challenge)
    {
        abort_if(!$challenge->is_active, 404);
        $challenge->load('levels');
        return view('student.games.play', compact('challenge'));
    }

    public function leaderboard()
    {
        $leaderboard = ChallengeAttempt::with('user')
            ->selectRaw('user_id, SUM(score) as total_score, COUNT(DISTINCT challenge_id) as challenges_played, MIN(time_taken) as best_time')
            ->where('is_completed', true)
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit(20)
            ->get();

        $challenges = Challenge::where('is_active', true)->withCount('levels')->get();

        return view('student.games.leaderboard', compact('leaderboard', 'challenges'));
    }
}
