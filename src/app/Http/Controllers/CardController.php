<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'question' => 'nullable|string|required_without:question_image',
            'answer' => 'nullable|string|required_without:answer_image',

            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'answer_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'category_id' => 'nullable|exists:categories,id',
            'return_category_id' => 'nullable|integer',
            'return_keyword' => 'nullable|string',
            'return_to' => 'nullable|in:study,cards',
        ]);

        $questionImagePath = $request->hasFile('question_image')
            ? $request->file('question_image')->store('cards/questions', 'public')
            : null;

        $answerImagePath = $request->hasFile('answer_image')
            ? $request->file('answer_image')->store('cards/answers', 'public')
            : null;

        $card = Card::create([
            'user_id' => auth()->id(),
            'question' => $request->question,
            'answer' => $request->answer,
            'question_image' => $questionImagePath,
            'answer_image' => $answerImagePath,
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
            'question' => 'nullable|string|required_without:question_image',
            'answer' => 'nullable|string|required_without:answer_image',

            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'answer_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'remove_question_image' => 'nullable|boolean',
            'remove_answer_image' => 'nullable|boolean',

            'category_id' => 'nullable|exists:categories,id',
            'return_category_id' => 'nullable|integer',
            'return_keyword' => 'nullable|string',
            'return_to' => 'nullable|in:study,cards',
        ]);

        $questionImagePath = $card->question_image;
        $answerImagePath = $card->answer_image;

        if ($request->boolean('remove_question_image') && $questionImagePath) {
            Storage::disk('public')->delete($questionImagePath);
            $questionImagePath = null;
        }

        if ($request->boolean('remove_answer_image') && $answerImagePath) {
            Storage::disk('public')->delete($answerImagePath);
            $answerImagePath = null;
        }

        if ($request->hasFile('question_image')) {
            if ($questionImagePath) {
                Storage::disk('public')->delete($questionImagePath);
            }

            $questionImagePath = $request->file('question_image')
                ->store('cards/questions', 'public');
        }

        if ($request->hasFile('answer_image')) {
            if ($answerImagePath) {
                Storage::disk('public')->delete($answerImagePath);
            }

            $answerImagePath = $request->file('answer_image')
                ->store('cards/answers', 'public');
        }

        /*
         * 編集時は既存画像も「問題/解答の内容」として扱うため、
         * 新しい画像が送られていない場合でも既存画像があれば
         * テキストを空にできます。
         */
        if (!$request->filled('question') && !$questionImagePath) {
            return back()
                ->withErrors(['question' => '問題文または問題画像のどちらかを入力してください。'])
                ->withInput();
        }

        if (!$request->filled('answer') && !$answerImagePath) {
            return back()
                ->withErrors(['answer' => '解答文または解答画像のどちらかを入力してください。'])
                ->withInput();
        }

        $card->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'question_image' => $questionImagePath,
            'answer_image' => $answerImagePath,
        ]);

        if ($request->filled('category_id')) {
            $category = $this->userCategories()
                ->findOrFail($request->category_id);

            $card->categories()->sync([$category->id]);
        } else {
            $card->categories()->detach();
        }

        if ($request->input('return_to') === 'study') {
            $studyParams = [];

            if ($request->filled('return_category_id')) {
                $studyParams['category_id'] = $request->input('return_category_id');
            }

            return redirect()
                ->route('study.index', $studyParams)
                ->with('success', 'カードを更新しました');
        }

        return redirect()
            ->route('cards.index', $this->returnFilters($request))
            ->with('success', 'カードを更新しました');
    }

    public function destroy(Card $card)
    {
        $this->authorizeCard($card);

        if ($card->question_image) {
            Storage::disk('public')->delete($card->question_image);
        }

        if ($card->answer_image) {
            Storage::disk('public')->delete($card->answer_image);
        }

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
