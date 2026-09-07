<?php

namespace App\Http\Controllers;

use App\Models\Memo;


use Illuminate\Http\Request;


class MemoController extends Controller
{
    public function index()
    {
        $memos = Memo::all();
        return view(
            'memo.index',
            compact('memos')
        );
    }

    public function store(Request $request)
    {
        Memo::create([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect('/memo')->with('success', '登録が完了しました');
    }


    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);

        $memo->title = $request->title;
        $memo->content = $request->content;

        $memo->save();

        return redirect('/memo')->with('success', '編集が完了しました');
    }

    public function destroy($id)
    {
        $memo = Memo::findOrFail($id);

        $memo->delete();

        return redirect('/memo')->with('success', '削除が完了しました');
    }
}