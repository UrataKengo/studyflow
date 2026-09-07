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
                'again' => '1分',
                'hard' => '10分',
                'good' => $this->calculateReviewDays('good', (int) $card->review_count) . '日',
                'easy' => $this->calculateReviewDays('easy', (int) $card->review_count) . '日',
            ]
            : null;

        $learningCount = $this->countLearningCards($categoryId);
        $newCount = $this->countNewCards($categoryId);
        $reviewCount = $this->countReviewCards($categoryId);
        $waitingLearningCount = $this->countWaitingLearningCards($categoryId);
        $nextLearningCard = $this->getNextWaitingLearningCard($categoryId);
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
            'nextLearningCard',
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
            ?? $this->reviewQueue($categoryId)->first();
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
        $query = Card::where('user_id', auth()->id())
            ->where('status', 'learning')
            ->where('review_at', '>', now());

        $this->applyCategoryFilter($query, $categoryId);

        return $query->count();
    }

    private function getNextWaitingLearningCard(?int $categoryId = null)
    {
        $query = Card::where('user_id', auth()->id())
            ->where('status', 'learning')
            ->where('review_at', '>', now());

        $this->applyCategoryFilter($query, $categoryId);

        return $query->orderBy('review_at')->first();
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
        if ($result === 'again') {
            return [0, 'learning', today(), now()->addMinute()];
        }

        if ($result === 'hard') {
            return [$card->review_count, 'learning', today(), now()->addMinutes(10)];
        }

        $oldReviewCount = $card->review_count;
        $newReviewCount = $oldReviewCount + 1;
        $days = $this->calculateReviewDays($result, $oldReviewCount);

        return [
            $newReviewCount,
            'review',
            now()->addDays($days)->toDateString(),
            null,
        ];
    }

    private function calculateReviewDays(string $result, int $reviewCount)
    {
        return match ($result) {
            'good' => match ($reviewCount) {
                0 => 7,
                1 => 14,
                2 => 30,
                default => 60,
            },
            'easy' => match ($reviewCount) {
                0 => 14,
                1 => 30,
                2 => 60,
                default => 120,
            },
        };
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
