<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カード登録</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>

    <div class="layout">

        <aside class="sidebar">

            <div class="logo">
                StudyFlow
            </div>

            <nav class="menu">
                <a href="{{ route('dashboard.index') }}">
                    ダッシュボード
                </a>

                <a class="active" href="{{ route('cards.index') }}">
                    カード一覧
                </a>

                <a href="{{ route('study-logs.index') }}">
                    学習履歴
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
            </form>

        </aside>

        <main class="content">

            <h1>カード登録</h1>

            @if ($errors->any())
                <ul style="color:red;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="card">

                <form action="{{ route('cards.store') }}" method="POST">
                    @csrf

                    <p>
                        <strong>問題</strong><br>
                        <textarea name="question" rows="4"></textarea>
                    </p>

                    <p>
                        <strong>答え</strong><br>
                        <textarea name="answer" rows="4"></textarea>
                    </p>

                    <p>
                        <strong>次回復習日</strong><br>
                        <input type="date" name="next_review_date">
                    </p>

                    <p>
                        <strong>カテゴリ</strong><br>

                        @foreach ($categories as $category)
                            <label>
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                    @checked(request('category_id') == $category->id)>
                                {{ $category->name }}
                            </label><br>
                        @endforeach
                    </p>

                    <br>

                    <button class="btn btn-primary" type="submit">
                        登録
                    </button>

                    <a class="btn btn-secondary" href="{{ route('cards.index') }}">
                        戻る
                    </a>

                </form>

            </div>

        </main>

    </div>

</body>

</html>