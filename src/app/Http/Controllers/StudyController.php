<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\StudyLog;
use Illuminate\Http\Request;

class StudyController extends Controller
{
    private const MIN_DIFFICULTY = 1.0;
    private const MAX_DIFFICULTY = 10.0;
    private const MIN_STABILITY = 0.25;
    private const MAX_STABILITY = 3650.0;
    private const TARGET_RETENTION = 0.90;

    public function index(Request $request)
    {
        $categoryId = $request->integer('category_id') ?: null;

        if ($categoryId && !$this->userCategoryExists($categoryId)) {
            $categoryId = null;
        }

        $card = $this->getNextStudyCard($categoryId);

        $answerIntervals = $card
            ? [
                'again' => '約1分',
                'hard' => '約10分',
                'good' => $this->formatReviewInterval(
                    $this->previewReviewDays($card, 'good')
                ),
                'easy' => $this->formatReviewInterval(
                    $this->previewReviewDays($card, 'easy')
                ),
            ]
            : null;

        $learningCount = $this->countLearningCards($categoryId);
        $newCount = $this->countNewCards($categoryId);
        $reviewCount = $this->countReviewCards($categoryId);
        $waitingLearningCount = $this->countWaitingLearningCards($categoryId);
        $todaySummary = $this->getTodayStudySummary($categoryId);

        $displayLearningCount = $learningCount + $waitingLearningCount;
        $total = $displayLearningCount + $newCount + $reviewCount;

        $categories = Category::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        $selectedCategory = $categoryId
            ? $categories->firstWhere('id', $categoryId)
            : null;

        $categoryRemainingCounts = [];

        foreach ($categories as $category) {
            $categoryRemainingCounts[$category->id] =
                $this->countLearningCards($category->id)
                + $this->countWaitingLearningCards($category->id)
                + $this->countNewCards($category->id)
                + $this->countReviewCards($category->id);
        }

        $allRemainingCount =
            $this->countLearningCards()
            + $this->countWaitingLearningCards()
            + $this->countNewCards()
            + $this->countReviewCards();

        return view('study.index', compact(
            'card',
            'total',
            'learningCount',
            'displayLearningCount',
            'newCount',
            'reviewCount',
            'waitingLearningCount',
            'answerIntervals',
            'todaySummary',
            'categories',
            'categoryId',
            'selectedCategory',
            'categoryRemainingCounts',
            'allRemainingCount'
        ));
    }

    public function result(Request $request, $id)
    {
        $request->validate([
            'result' => 'required|in:again,hard,good,easy',
            'category_id' => 'nullable|integer',
        ]);

        $card = Card::where('user_id', auth()->id())
            ->findOrFail($id);

        $result = $request->result;
        $level = $this->calculateLevel($card, $result);
        $schedule = $this->calculateAdaptiveSchedule($card, $result);

        $card->update([
            'level' => $level,
            'review_count' => $schedule['review_count'],
            'study_count' => (int) $card->study_count + 1,
            'difficulty' => $schedule['difficulty'],
            'stability' => $schedule['stability'],
            'lapses' => $schedule['lapses'],
            'last_reviewed_at' => now(),
            'next_review_date' => $schedule['next_review_date'],
            'review_at' => $schedule['review_at'],
            'status' => $schedule['status'],
        ]);

        StudyLog::create([
            'card_id' => $card->id,
            'result' => $result,
            'studied_at' => now(),
        ]);

        $redirectParameters = [];

        if ($request->filled('category_id')) {
            $categoryId = (int) $request->input('category_id');

            if ($this->userCategoryExists($categoryId)) {
                $redirectParameters['category_id'] = $categoryId;
            }
        }

        return redirect()
            ->route('study.index', $redirectParameters)
            ->with('success', '学習結果を登録しました');
    }

    private function getNextStudyCard(?int $categoryId = null)
    {
        return $this->learningQueue($categoryId)->first()
            ?? $this->newQueue($categoryId)->first()
            ?? $this->reviewQueue($categoryId)->first()
            ?? $this->waitingLearningQueue($categoryId)->first();
    }

    private function learningQueue(?int $categoryId = null)
    {
        $query = Card::where('user_id', auth()->id())
            ->where('status', 'learning')
            ->where(function ($query) {
                $query->whereNull('review_at')
                    ->orWhere('review_at', '<=', now());
            });

        $this->applyCategoryFilter($query, $categoryId);

        return $query->orderBy('review_at');
    }

    private function waitingLearningQueue(?int $categoryId = null)
    {
        $query = Card::where('user_id', auth()->id())
            ->where('status', 'learning')
            ->where('review_at', '>', now());

        $this->applyCategoryFilter($query, $categoryId);

        return $query->orderBy('review_at');
    }

    private function newQueue(?int $categoryId = null)
    {
        $query = Card::where('user_id', auth()->id())
            ->where('status', 'new');

        $this->applyCategoryFilter($query, $categoryId);

        return $query->orderBy('created_at');
    }

    private function reviewQueue(?int $categoryId = null)
    {
        $query = Card::where('user_id', auth()->id())
            ->where('status', 'review')
            ->whereDate('next_review_date', '<=', today());

        $this->applyCategoryFilter($query, $categoryId);

        return $query->orderBy('next_review_date');
    }

    private function applyCategoryFilter($query, ?int $categoryId = null): void
    {
        if (!$categoryId) {
            return;
        }

        $query->whereHas('categories', function ($query) use ($categoryId) {
            $query->where('categories.id', $categoryId);
        });
    }

    private function countLearningCards(?int $categoryId = null)
    {
        return $this->learningQueue($categoryId)->count();
    }

    private function countNewCards(?int $categoryId = null)
    {
        return $this->newQueue($categoryId)->count();
    }

    private function countReviewCards(?int $categoryId = null)
    {
        return $this->reviewQueue($categoryId)->count();
    }

    private function countWaitingLearningCards(?int $categoryId = null)
    {
        return $this->waitingLearningQueue($categoryId)->count();
    }

    private function calculateLevel(Card $card, string $result): int
    {
        return match ($result) {
            'again' => max(1, (int) $card->level - 1),
            'hard' => (int) $card->level,
            'good' => min(5, (int) $card->level + 1),
            'easy' => min(5, (int) $card->level + 2),
        };
    }

    /**
     * StudyFlow Adaptive Review
     *
     * difficulty : 1.0（易しい）〜10.0（難しい）
     * stability  : 記憶が安定している期間の目安（日）
     * retrievability : 現時点で思い出せる確率の推定値
     */
    private function calculateAdaptiveSchedule(Card $card, string $result): array
    {
        $difficulty = $this->normalizeDifficulty((float) ($card->difficulty ?? 5.0));
        $stability = $this->normalizeStability((float) ($card->stability ?? 1.0));
        $lapses = max(0, (int) ($card->lapses ?? 0));
        $reviewCount = max(0, (int) $card->review_count);

        $elapsedDays = $this->getElapsedDays($card);
        $retrievability = $this->calculateRetrievability($stability, $elapsedDays);

        if ($result === 'again') {
            $consecutiveAgain = $this->getConsecutiveAgainCount($card);

            $difficulty = $this->normalizeDifficulty(
                $difficulty + 0.8 + min(0.6, $consecutiveAgain * 0.2)
            );

            $stabilityLoss = max(0.30, 0.55 - ($consecutiveAgain * 0.08));
            $stability = $this->normalizeStability($stability * $stabilityLoss);

            return [
                'review_count' => max(0, $reviewCount - 1),
                'difficulty' => $difficulty,
                'stability' => $stability,
                'lapses' => $lapses + 1,
                'status' => 'learning',
                'next_review_date' => today(),
                'review_at' => now()->addMinute(),
            ];
        }

        if ($result === 'hard') {
            $difficulty = $this->normalizeDifficulty($difficulty + 0.25);

            $growth = 1.08
                + (0.12 * (1.0 - $retrievability))
                + (0.03 * (10.0 - $difficulty));

            $stability = $this->normalizeStability($stability * $growth);

            return [
                'review_count' => $reviewCount,
                'difficulty' => $difficulty,
                'stability' => $stability,
                'lapses' => $lapses,
                'status' => 'learning',
                'next_review_date' => today(),
                'review_at' => now()->addMinutes(10),
            ];
        }

        $difficultyChange = $result === 'easy' ? -0.55 : -0.15;
        $difficulty = $this->normalizeDifficulty($difficulty + $difficultyChange);

        $stability = $this->calculateNewStability(
            $stability,
            $difficulty,
            $retrievability,
            $result
        );

        $days = $this->calculateIntervalDays($stability, $result);

        return [
            'review_count' => $result === 'easy'
                ? min(9999, $reviewCount + 2)
                : min(9999, $reviewCount + 1),
            'difficulty' => $difficulty,
            'stability' => $stability,
            'lapses' => $lapses,
            'status' => 'review',
            'next_review_date' => now()->addDays($days)->toDateString(),
            'review_at' => null,
        ];
    }

    /**
     * Good / Easy を押した場合の次回間隔を、
     * 保存前のカード状態からプレビューします。
     */
    private function previewReviewDays(Card $card, string $result): int
    {
        $difficulty = $this->normalizeDifficulty((float) ($card->difficulty ?? 5.0));
        $stability = $this->normalizeStability((float) ($card->stability ?? 1.0));

        $elapsedDays = $this->getElapsedDays($card);
        $retrievability = $this->calculateRetrievability($stability, $elapsedDays);

        $difficulty += $result === 'easy' ? -0.55 : -0.15;
        $difficulty = $this->normalizeDifficulty($difficulty);

        $newStability = $this->calculateNewStability(
            $stability,
            $difficulty,
            $retrievability,
            $result
        );

        return $this->calculateIntervalDays($newStability, $result);
    }

    /**
     * 経過時間と現在の安定度から記憶保持率を推定します。
     *
     * R = e^(-t / S)
     */
    private function calculateRetrievability(float $stability, float $elapsedDays): float
    {
        if ($elapsedDays <= 0) {
            return 1.0;
        }

        return max(
            0.01,
            min(1.0, exp(-$elapsedDays / max(self::MIN_STABILITY, $stability)))
        );
    }

    /**
     * 正答時のStabilityを更新します。
     *
     * 忘れかけた状態で思い出せた場合ほど記憶強化を大きくし、
     * 難しいカードほど伸びを抑えます。
     */
    private function calculateNewStability(
        float $stability,
        float $difficulty,
        float $retrievability,
        string $result
    ): float {
        $difficultyFactor = 1.15 - (0.055 * $difficulty);
        $retrievalBonus = 1.0 + (1.0 - $retrievability);

        $answerFactor = match ($result) {
            'good' => 1.65,
            'easy' => 2.25,
        };

        $growth = max(
            1.05,
            $answerFactor * $difficultyFactor * $retrievalBonus
        );

        return $this->normalizeStability($stability * $growth);
    }

    /**
     * 目標保持率を基準に、Stabilityから次回復習日を決めます。
     */
    private function calculateIntervalDays(float $stability, string $result): int
    {
        $baseDays = -$stability * log(self::TARGET_RETENTION);

        if ($result === 'easy') {
            $baseDays *= 1.35;
        }

        $minimumDays = $result === 'easy' ? 2 : 1;

        return max(
            $minimumDays,
            min(3650, (int) round($baseDays))
        );
    }

    private function getElapsedDays(Card $card): float
    {
        if (!$card->last_reviewed_at) {
            return 0.0;
        }

        $seconds = $card->last_reviewed_at->diffInSeconds(now());

        return max(0.0, $seconds / 86400);
    }

    private function normalizeDifficulty(float $difficulty): float
    {
        return round(
            max(self::MIN_DIFFICULTY, min(self::MAX_DIFFICULTY, $difficulty)),
            2
        );
    }

    private function normalizeStability(float $stability): float
    {
        return round(
            max(self::MIN_STABILITY, min(self::MAX_STABILITY, $stability)),
            2
        );
    }

    private function formatReviewInterval(int $days): string
    {
        if ($days < 30) {
            return '約' . $days . '日';
        }

        if ($days < 365) {
            $months = round($days / 30, 1);

            return '約' . $months . 'か月';
        }

        $years = round($days / 365, 1);

        return '約' . $years . '年';
    }

    private function getConsecutiveAgainCount(Card $card): int
    {
        $logs = StudyLog::where('card_id', $card->id)
            ->orderByDesc('studied_at')
            ->limit(10)
            ->pluck('result');

        $count = 0;

        foreach ($logs as $result) {
            if ($result !== 'again') {
                break;
            }

            $count++;
        }

        return $count;
    }

    private function getTodayStudySummary(?int $categoryId = null): array
    {
        $query = StudyLog::whereDate('studied_at', today())
            ->whereHas('card', function ($query) use ($categoryId) {
                $query->where('user_id', auth()->id());

                if ($categoryId) {
                    $query->whereHas('categories', function ($query) use ($categoryId) {
                        $query->where('categories.id', $categoryId);
                    });
                }
            });

        $counts = (clone $query)
            ->selectRaw('result, COUNT(*) as total')
            ->groupBy('result')
            ->pluck('total', 'result');

        $total = (int) $counts->sum();
        $goodCount = (int) ($counts['good'] ?? 0);
        $easyCount = (int) ($counts['easy'] ?? 0);

        $correctRate = $total > 0
            ? round((($goodCount + $easyCount) / $total) * 100)
            : 0;

        return [
            'total' => $total,
            'again' => (int) ($counts['again'] ?? 0),
            'hard' => (int) ($counts['hard'] ?? 0),
            'good' => $goodCount,
            'easy' => $easyCount,
            'correct_rate' => $correctRate,
        ];
    }

    private function userCategoryExists(int $categoryId): bool
    {
        return Category::where('user_id', auth()->id())
            ->where('id', $categoryId)
            ->exists();
    }
}
