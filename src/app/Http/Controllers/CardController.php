<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $categoryId = $request->input('category_id');

        $query = Card::with('categories')
            ->where('user_id', auth()->id());

        if (!empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('question', 'like', '%' . $keyword . '%')
                    ->orWhere('answer', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($categoryId)) {
            $query->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            });
        }

        $cards = $query->latest()->get();

        $categories = $this->userCategories()
            ->withCount('cards')
            ->latest()
            ->get();

        return view('cards.index', compact(
            'cards',
            'keyword',
            'categories',
            'categoryId'
        ));
    }

    public function create()
    {
        $categories = $this->userCategories()
            ->latest()
            ->get();

        return view('cards.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'return_category_id' => 'nullable|integer',
            'return_keyword' => 'nullable|string',
        ]);

        $card = Card::create([
            'user_id' => auth()->id(),
            'question' => $request->question,
            'answer' => $request->answer,
            'next_review_date' => today(),
            'level' => 1,
            'review_count' => 0,
            'study_count' => 0,
            'status' => 'new',
            'review_at' => null,
        ]);

        if ($request->filled('category_id')) {
            $category = $this->userCategories()
                ->findOrFail($request->category_id);

            $card->categories()->attach($category->id);
        }

        return redirect()
            ->route('cards.index', $this->returnFilters($request))
            ->with('success', 'カードを登録しました');
    }

    public function show(Card $card)
    {
        $this->authorizeCard($card);

        return view('cards.show', compact('card'));
    }

    public function edit(Card $card)
    {
        $this->authorizeCard($card);

        $categories = $this->userCategories()
            ->latest()
            ->get();

        return view('cards.edit', compact(
            'card',
            'categories'
        ));
    }

    public function update(Request $request, Card $card)
    {
        $this->authorizeCard($card);

        $request->validate([
            'question' => 'required',
            'answer' => 'required',
            'category_id' => 'nullable|exists:categories,id',
            'return_category_id' => 'nullable|integer',
            'return_keyword' => 'nullable|string',
        ]);

        $card->update([
            'question' => $request->question,
            'answer' => $request->answer,
        ]);

        if ($request->filled('category_id')) {
            $category = $this->userCategories()
                ->findOrFail($request->category_id);

            $card->categories()->sync([$category->id]);
        } else {
            $card->categories()->detach();
        }

        return redirect()
            ->route('cards.index', $this->returnFilters($request))
            ->with('success', 'カードを更新しました');
    }

    public function destroy(Card $card)
    {
        $this->authorizeCard($card);

        $card->studyLogs()->delete();
        $card->categories()->detach();
        $card->delete();

        return redirect()
            ->route('cards.index')
            ->with('success', 'カードを削除しました');
    }

    private function userCategories()
    {
        return Category::where('user_id', auth()->id());
    }

    private function authorizeCard(Card $card): void
    {
        if ($card->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function returnFilters(Request $request): array
    {
        return array_filter([
            'category_id' => $request->input('return_category_id'),
            'keyword' => $request->input('return_keyword'),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
