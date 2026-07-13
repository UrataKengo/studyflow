<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学習履歴</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">
            <div class="logo">StudyFlow</div>

            <nav class="menu">
                <a href="{{ route('dashboard.index') }}">ダッシュボード</a>
                <a href="{{ route('cards.index') }}">カード一覧</a>
                <a href="{{ route('categories.index') }}">カテゴリ</a>
                <a href="{{ route('study.index') }}">学習開始</a>
                <a class="active" href="{{ route('study-logs.index') }}">学習履歴</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
            </form>
        </aside>

        <main class="content">

            <h1>学習履歴</h1>

            <table class="card-table">
                <thead>
                    <tr>
                        <th>学習日時</th>
                        <th>問題</th>
                        <th>カテゴリ</th>
                        <th>結果</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->studied_at }}</td>

                            <td>{{ $log->card->question ?? 'カードなし' }}</td>

                            <td>
                                @if ($log->card && $log->card->categories->count())
                                    @foreach ($log->card->categories as $category)
                                        {{ $category->name }}@if (!$loop->last), @endif
                                    @endforeach
                                @else
                                    未分類
                                @endif
                            </td>

                            <td>
                                @switch($log->result)
                                    @case('again')
                                        もう一度
                                        @break

                                    @case('hard')
                                        難しい
                                        @break

                                    @case('good')
                                        良い
                                        @break

                                    @case('easy')
                                        簡単
                                        @break

                                    @default
                                        不明
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">まだ学習履歴がありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </main>

    </div>
</body>

</html>