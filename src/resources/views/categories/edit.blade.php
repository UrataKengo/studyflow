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

                <a href="{{ route('study.index') }}">
                    学習開始
                </a>

            </nav>

            <form
                action="{{ route('logout') }}"
                method="POST"
                style="margin-top:20px;"
            >
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>

            </form>

        </aside>


        <main class="content">

            <h1>カテゴリ編集</h1>


            @if ($errors->any())

                <div class="error-message">

                    @foreach ($errors->all() as $error)

                        <div>
                            {{ $error }}
                        </div>

                    @endforeach

                </div>

            @endif


            <div class="card">

                <form
                    action="{{ route('categories.update', $category->id) }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')


                    <div class="form-group">

                        <label for="name">
                            カテゴリ名
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $category->name) }}"
                            required
                        >

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            更新
                        </button>

                        <a
                            href="{{ route('cards.index', [
                                'category_id' => $category->id
                            ]) }}"
                            class="btn btn-secondary"
                        >
                            戻る
                        </a>

                    </div>

                </form>

            </div>

        </main>

    </div>

</body>

</html>