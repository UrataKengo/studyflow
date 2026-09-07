<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カテゴリ管理</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">
            <div class="logo">StudyFlow</div>

            <nav class="menu">
                <a href="{{ route('dashboard.index') }}">ダッシュボード</a>
                <a href="{{ route('cards.index') }}">カード一覧</a>
                <a href="{{ route('study.index') }}">学習開始</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
            </form>
        </aside>

        <main class="content">

            <h1>カテゴリ管理</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <input type="text" name="name" placeholder="カテゴリ名を入力" required>

                <button type="submit">
                    登録
                </button>
            </form>

            <hr>

            <table class="card-table">
                <thead>
                    <tr>
                        <th>カテゴリ名</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="category-name-cell">
                                <span class="category-title">
                                    📚 {{ $category->name }}
                                </span>

                                <div class="card-options">
                                    <button type="button" class="option-btn">︙</button>

                                    <div class="option-menu">
                                        <a href="{{ route('categories.edit', $category->id) }}">
                                            編集
                                        </a>

                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                            onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td>
                                カテゴリがありません
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </main>

    </div>
</body>

</html>