<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Category::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
        ]);

        return redirect()
            ->route('cards.index')
            ->with('success', 'カテゴリを登録しました');
    }

    public function edit(Category $category)
    {
        $this->authorizeCategory($category);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $request->validate([
            'name' => 'required|max:255',
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('cards.index', ['category_id' => $category->id])
            ->with('success', 'カテゴリを更新しました');
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);

        DB::transaction(function () use ($category) {
            $cards = $category->cards()->get();

            foreach ($cards as $card) {
                $card->studyLogs()->delete();
                $card->categories()->detach();
                $card->delete();
            }

            $category->delete();
        });

        return redirect()
            ->route('cards.index')
            ->with('success', 'カテゴリと関連するカードを削除しました。');
    }

    private function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
