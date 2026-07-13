<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カテゴリ編集</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">
            <div class="logo">StudyFlow</div>

            <nav class="menu">
                <a href="{{ route('dashboard.index') }}">ダッシュボード</a>
                <a href="{{ route('cards.index') }}">カード一覧</a>
                <a class="active" href="{{ route('categories.index') }}">カテゴリ</a>
                <a href="{{ route('study-logs.index') }}">学習履歴</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
            </form>
        </aside>

        <main class="content">
            <h1>カテゴリ編集</h1>

            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <p>
                    <strong>カテゴリ名</strong><br>
                    <input type="text" name="name" value="{{ $category->name }}" required>
                </p>

                <button type="submit">更新</button>

                <a href="{{ route('categories.index') }}">戻る</a>
            </form>
        </main>

    </div>
</body>

</html>