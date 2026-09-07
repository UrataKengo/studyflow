<?php
//DB担当
// このファイルが App\Models という場所にあることを表す
namespace App\Models;

// Eloquent(Model)を利用するために読み込む
// Modelを継承することでデータベース操作ができるようになる
use Illuminate\Database\Eloquent\Model;

/**
 * Memoモデル
 *
 * memosテーブルを操作するためのクラス
 *
 * 主な役割
 * ・データの取得
 * ・データの登録
 * ・データの更新
 * ・データの削除
 *
 * Controllerから呼び出して使用する
 */
class Memo extends Model
{
    /**
     * 一括代入（Mass Assignment）を許可するカラム
     *
     * Memo::create([
     *     'title' => 'タイトル',
     *     'content' => '内容'
     * ]);
     *
     * のような記述を行うために必要
     *
     * 指定していないカラムは create() や update() で
     * 一括代入できないためセキュリティ向上にもなる
     */
    protected $fillable = [
        // メモのタイトル
        'title',

        // メモの本文
        'content'
    ];
}