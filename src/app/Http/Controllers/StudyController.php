<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\StudyLog;
use Illuminate\Http\Request;

class StudyController extends Controller
{
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
                'good' => $this->calculateReviewDays('good', (int) $card->review_count) . '日',
                'easy' => $this->calculateReviewDays('easy', (int) $card->review_count) . '日',
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

        [$reviewCount, $status, $nextReviewDate, $reviewAt] =
            $this->calculateReviewSchedule($card, $result);

        $card->update([
            'level' => $level,
            'review_count' => $reviewCount,
            'study_count' => $card->study_count + 1,
            'next_review_date' => $nextReviewDate,
            'review_at' => $reviewAt,
            'status' => $status,
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

    private function calculateLevel(Card $card, string $result)
    {
        return match ($result) {
            'again' => max(1, $card->level - 1),
            'hard' => $card->level,
            'good' => min(5, $card->level + 1),
            'easy' => min(5, $card->level + 2),
        };
    }

    private function calculateReviewSchedule(Card $card, string $result)
    {
        $currentReviewCount = min(5, max(0, (int) $card->review_count));

        if ($result === 'again') {
            $recentAgainCount = $this->getConsecutiveAgainCount($card);

            $stepsBack = match (true) {
                $recentAgainCount >= 2 => 3,
                $recentAgainCount === 1 => 2,
                default => 1,
            };

            return [
                max(0, $currentReviewCount - $stepsBack),
                'learning',
                today(),
                now()->addMinute(),
            ];
        }

        if ($result === 'hard') {
            return [
                max(0, $currentReviewCount - 1),
                'learning',
                today(),
                now()->addMinutes(10),
            ];
        }

        $days = $this->calculateReviewDays($result, $currentReviewCount);

        $newReviewCount = match ($result) {
            'good' => min(5, $currentReviewCount + 1),
            'easy' => min(5, $currentReviewCount + 2),
        };

        return [
            $newReviewCount,
            'review',
            now()->addDays($days)->toDateString(),
            null,
        ];
    }

    /**
     * 直近の連続Again回数を取得します。
     *
     * 例:
     * 直近が Good → 0
     * 直近が Again → 1
     * 直近が Again, Again → 2
     *
     * 今回押したAgainはまだStudyLogに保存される前なので、
     * ここでは「今回の直前までに何回連続Againだったか」を数えます。
     */
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

    private function calculateReviewDays(string $result, int $reviewCount)
    {
        $intervals = [1, 3, 7, 14, 30, 60];

        $reviewCount = min(5, max(0, $reviewCount));

        $stage = match ($result) {
            'good' => $reviewCount,
            'easy' => min(5, $reviewCount + 1),
        };

        return $intervals[$stage];
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
