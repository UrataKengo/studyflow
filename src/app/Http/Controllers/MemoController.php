<?php
//司令塔
// このファイルが App\Http\Controllers という場所にあることを表す
namespace App\Http\Controllers;

// Memoモデルを使うために読み込む
// memosテーブルとやり取りするために必要
use App\Models\Memo;

// フォームから送られてきたデータを受け取るために必要
use Illuminate\Http\Request;

// MemoController クラス
// メモアプリの「表示・登録・更新・削除」の処理を書く場所
class MemoController extends Controller
{
    /**
     * メモ一覧画面を表示する処理
     *
     * URL例：
     * GET /memo
     */
    public function index()
    {
        // memosテーブルのデータをすべて取得する
        // SELECT * FROM memos; とほぼ同じ意味
        $memos = Memo::all();

        // resources/views/memo/index.blade.php を表示する
        // compact('memos') によって、画面側で $memos が使えるようになる
        return view(
            'memo.index',
            compact('memos')
        );
    }

    /**
     * 新しいメモを登録する処理
     *
     * URL例：
     * POST /memo/store
     */
    public function store(Request $request)
    {
        // フォームから送られてきた title と content を
        // memosテーブルに新しく保存する
        Memo::create([
            // name="title" の入力内容を保存
            'title' => $request->title,

            // name="content" の入力内容を保存
            'content' => $request->content
        ]);

        // 登録後、メモ一覧画面に戻る
        return redirect('/memo')
            ->with('success', '📝 登録が完了しました');
    }

    /**
     * 既存のメモを更新する処理
     *
     * URL例：
     * POST /memo/update/{id}
     */
    public function update(Request $request, $id)
    {
        $memo = Memo::findOrFail($id);

        $memo->title = $request->title;
        $memo->content = $request->content;

        $memo->save();

        return redirect('/memo')->with('success', '編集が完了しました');
    }

    /**
     * メモを削除する処理
     *
     * URL例：
     * POST /memo/delete/{id}
     */
    public function destroy($id)
    {
        // URLから受け取った id のメモを探す
        // 見つからなければ 404 エラーになる
        $memo = Memo::findOrFail($id);

        // 見つけたメモをデータベースから削除する
        $memo->delete();

        // 削除後、メモ一覧画面に戻る
        return redirect('/memo')->with('success', '削除が完了しました');
    }
}