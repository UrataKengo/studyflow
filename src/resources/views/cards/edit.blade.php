<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カード編集</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>

    <div class="layout">

        <aside class="sidebar">

            <div class="logo">
                StudyFlow
            </div>

            <nav class="menu">
                <a href="#">ダッシュボード</a>

                <a class="active" href="{{ route('cards.index') }}">
                    カード一覧
                </a>

                <a href="#">カテゴリ</a>
                <a href="#">学習履歴</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
            </form>
            
        </aside>

        <main class="content">

            <h1>カード編集</h1>

            <div class="card">

                <form action="{{ route('cards.update', $card) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <p>
                        <strong>問題</strong><br>
                        <textarea name="question" rows="4">{{ $card->question }}</textarea>
                    </p>

                    <p>
                        <strong>答え</strong><br>
                        <textarea name="answer" rows="4">{{ $card->answer }}</textarea>
                    </p>

                    <p>
                        <strong>次回復習日</strong><br>
                        <input type="date" name="next_review_date" value="{{ $card->next_review_date }}">
                    </p>

                    <p>
                        <strong>カテゴリ</strong><br>

                        @foreach ($categories as $category)
                            <label>
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                    @checked($card->categories->contains($category->id))>
                                {{ $category->name }}
                            </label><br>
                        @endforeach
                    </p>

                    <br>

                    <button class="btn btn-primary" type="submit">
                        更新
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