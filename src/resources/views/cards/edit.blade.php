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

                <button
                    type="submit"
                    class="logout-btn"
                >
                    ログアウト
                </button>

            </form>

        </aside>


        <main class="content">

            <h1>カード編集</h1>


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
                    action="{{ route('cards.update', $card) }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')


                    <div class="form-group">

                        <label for="question">
                            問題
                        </label>

                        <textarea
                            id="question"
                            name="question"
                            rows="4"
                            required
                        >{{ old('question', $card->question) }}</textarea>

                    </div>


                    <div class="form-group">

                        <label for="answer">
                            答え
                        </label>

                        <textarea
                            id="answer"
                            name="answer"
                            rows="4"
                            required
                        >{{ old('answer', $card->answer) }}</textarea>

                    </div>


                    <div class="form-group">

                        <label for="category_id">
                            カテゴリ
                        </label>

                        @php
                            $selectedCategoryId = old(
                                'category_id',
                                optional($card->categories->first())->id
                            );
                        @endphp

                        <select
                            id="category_id"
                            name="category_id"
                        >

                            <option value="">
                                カテゴリなし
                            </option>


                            @foreach ($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        (string) $selectedCategoryId
                                        ===
                                        (string) $category->id
                                    )
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="form-group">

                        <label for="next_review_date">
                            次回復習日
                        </label>

                        <input
                            id="next_review_date"
                            type="date"
                            name="next_review_date"
                            value="{{ old(
                                'next_review_date',
                                $card->next_review_date
                                    ? $card->next_review_date->format('Y-m-d')
                                    : ''
                            ) }}"
                        >

                    </div>


                    <div class="form-actions">

                        <button
                            class="btn btn-primary"
                            type="submit"
                        >
                            更新
                        </button>

                        <a
                            class="btn btn-secondary"
                            href="{{ route('cards.index') }}"
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