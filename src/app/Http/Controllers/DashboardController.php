<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\StudyLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today = Carbon::today();
        $now = Carbon::now();

        // -------------------------
        // カード状態
        // -------------------------
        $newCardCount = Card::where('user_id', $userId)
            ->where('status', 'new')
            ->count();

        $learningCardCount = Card::where('user_id', $userId)
            ->where('status', 'learning')
            ->count();

        $availableLearningCount = Card::where('user_id', $userId)
            ->where('status', 'learning')
            ->where(function ($query) use ($now) {
                $query->whereNull('review_at')
                    ->orWhere('review_at', '<=', $now);
            })
            ->count();

        $waitingLearningCount = Card::where('user_id', $userId)
            ->where('status', 'learning')
            ->where('review_at', '>', $now)
            ->count();

        $reviewCardCount = Card::where('user_id', $userId)
            ->where('status', 'review')
            ->whereDate('next_review_date', '<=', $today)
            ->count();

        $otherCardCount = Card::where('user_id', $userId)
            ->where('status', 'review')
            ->whereDate('next_review_date', '>', $today)
            ->count();

        $totalCardCount = Card::where('user_id', $userId)->count();

        // 今すぐ学習できるカード
        $todayStudyCardCount = $newCardCount
            + $availableLearningCount
            + $reviewCardCount;

        // -------------------------
        // 今日の学習実績
        // -------------------------
        $todayLogsQuery = $this->studyLogsForUser($userId)
            ->whereDate('studied_at', $today);

        $todayStudyCount = (clone $todayLogsQuery)->count();

        $todayCorrectCount = (clone $todayLogsQuery)
            ->whereIn('result', ['good', 'easy'])
            ->count();

        $accuracyRate = $todayStudyCount > 0
            ? round(($todayCorrectCount / $todayStudyCount) * 100)
            : 0;

        // -------------------------
        // 直近7日間の学習回数
        // -------------------------
        $startDate = $today->copy()->subDays(6)->startOfDay();
        $endDate = $today->copy()->endOfDay();

        $studyCountsByDate = $this->studyLogsForUser($userId)
            ->whereBetween('studied_at', [$startDate, $endDate])
            ->selectRaw('DATE(studied_at) as study_date, COUNT(*) as study_count')
            ->groupBy('study_date')
            ->pluck('study_count', 'study_date');

        $studyChartLabels = [];
        $studyChartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $dateKey = $date->format('Y-m-d');

            $studyChartLabels[] = $date->format('m/d');
            $studyChartData[] = (int) ($studyCountsByDate[$dateKey] ?? 0);
        }

        return view('dashboard.index', compact(
            'todayStudyCardCount',
            'newCardCount',
            'learningCardCount',
            'reviewCardCount',
            'todayStudyCount',
            'accuracyRate',
            'studyChartLabels',
            'studyChartData',
            'totalCardCount',
            'otherCardCount',
            'availableLearningCount',
            'waitingLearningCount'
        ));
    }

    private function studyLogsForUser(int $userId)
    {
        return StudyLog::whereHas('card', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}
