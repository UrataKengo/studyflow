<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カード編集 | StudyFlow</title>

    <link rel="stylesheet" href="{{ asset('css/cards-form.css') }}?v=20260908-unified">
</head>

<body class="card-form-page-bg">

    <div class="card-form-background" aria-hidden="true">
        <span class="card-form-decor-circle card-form-decor-left-top"></span>
        <span class="card-form-decor-circle card-form-decor-left-bottom"></span>
        <span class="card-form-decor-circle card-form-decor-right-top"></span>
        <span class="card-form-decor-circle card-form-decor-right-bottom"></span>
        <span class="card-form-decor-line card-form-decor-line-left"></span>
        <span class="card-form-decor-line card-form-decor-line-right"></span>
    </div>

    <aside class="card-form-side-copy card-form-side-copy-left" aria-hidden="true">
        SMALL STEPS<br>
        BIG CHANGES
    </aside>

    <aside class="card-form-side-copy card-form-side-copy-right-top" aria-hidden="true">
        A Better You<br>
        One Card at a Time.
        <span></span>
    </aside>

    <aside class="card-form-side-copy card-form-side-copy-right-bottom" aria-hidden="true">
        学ぶことが、<br>
        きっと楽しくなる。
        <span></span>
    </aside>

    <header class="card-form-topbar">
        <div class="card-form-brand">
            <a href="{{ route('dashboard.index') }}" class="card-form-logo">
                StudyFlow
            </a>

            <span class="card-form-divider"></span>

            <span class="card-form-page">
                カード編集
            </span>
        </div>

        @if (request('from') === 'study')
            <a
                href="{{ route('study.index', request()->filled('category_id') ? ['category_id' => request('category_id')] : []) }}"
                class="card-form-back"
            >
                ← 学習画面へ戻る
            </a>
        @else
            <a href="{{ route('cards.index') }}" class="card-form-back">
                ← カード管理へ戻る
            </a>
        @endif
    </header>

    <main class="card-form-content">

        <div class="card-form-page-top">
            <div>
                <div class="card-form-breadcrumb">
                    <a href="{{ route('cards.index') }}">カード管理</a>
                    <span>›</span>
                    <span>カード編集</span>
                </div>

                <div class="card-form-heading">
                    <h1>カード編集</h1>
                    <p>問題・答え・画像・カテゴリを編集できます。</p>
                </div>
            </div>

        </div>

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form
            action="{{ route('cards.update', $card) }}"
            method="POST"
            enctype="multipart/form-data"
            class="card-form-panel"
        >
            @csrf
            @method('PUT')

            @if (request('from') === 'study')
                <input type="hidden" name="return_to" value="study">

                @if (request()->filled('category_id'))
                    <input
                        type="hidden"
                        name="return_category_id"
                        value="{{ request('category_id') }}"
                    >
                @endif
            @endif

            <div class="card-form-grid">

                <div class="form-section">
                    <label class="form-label" for="question">
                        問題
                    </label>

                    <textarea
                        id="question"
                        name="question"
                        placeholder="問題文を入力してください"
                    >{{ old('question', $card->question) }}</textarea>

                    <span class="form-hint">
                        問題文または問題画像のどちらか一方があれば保存できます。
                    </span>
                </div>

                <div class="form-section">
                    <label class="form-label" for="question_image">
                        問題画像
                    </label>

                    <div class="image-upload-box">

                        @if ($card->question_image)
                            <div class="current-image" id="currentQuestionImage">
                                <span class="current-image-label">現在の画像</span>

                                <img
                                    src="{{ asset('storage/' . $card->question_image) }}"
                                    alt="現在の問題画像"
                                >

                                <label class="remove-image">
                                    <input
                                        type="checkbox"
                                        name="remove_question_image"
                                        value="1"
                                        id="removeQuestionImage"
                                        @checked(old('remove_question_image'))
                                    >
                                    現在の画像を削除
                                </label>
                            </div>
                        @endif

                        <input
                            id="question_image"
                            type="file"
                            name="question_image"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            data-preview-target="questionImagePreview"
                        >

                        <span class="form-hint">
                            新しい画像を選ぶと現在の画像を置き換えます。最大5MB
                        </span>

                        <div class="image-preview" id="questionImagePreview">
                            <img src="" alt="新しい問題画像プレビュー">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <label class="form-label" for="answer">
                        答え
                    </label>

                    <textarea
                        id="answer"
                        name="answer"
                        placeholder="答えを入力してください"
                    >{{ old('answer', $card->answer) }}</textarea>

                    <span class="form-hint">
                        解答文または解答画像のどちらか一方があれば保存できます。
                    </span>
                </div>

                <div class="form-section">
                    <label class="form-label" for="answer_image">
                        解答画像
                    </label>

                    <div class="image-upload-box">

                        @if ($card->answer_image)
                            <div class="current-image" id="currentAnswerImage">
                                <span class="current-image-label">現在の画像</span>

                                <img
                                    src="{{ asset('storage/' . $card->answer_image) }}"
                                    alt="現在の解答画像"
                                >

                                <label class="remove-image">
                                    <input
                                        type="checkbox"
                                        name="remove_answer_image"
                                        value="1"
                                        id="removeAnswerImage"
                                        @checked(old('remove_answer_image'))
                                    >
                                    現在の画像を削除
                                </label>
                            </div>
                        @endif

                        <input
                            id="answer_image"
                            type="file"
                            name="answer_image"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            data-preview-target="answerImagePreview"
                        >

                        <span class="form-hint">
                            新しい画像を選ぶと現在の画像を置き換えます。最大5MB
                        </span>

                        <div class="image-preview" id="answerImagePreview">
                            <img src="" alt="新しい解答画像プレビュー">
                        </div>
                    </div>
                </div>

                <div class="form-section full">
                    <label class="form-label" for="category_id">
                        カテゴリ
                    </label>

                    @php
                        $selectedCategoryId = old(
                            'category_id',
                            optional($card->categories->first())->id
                        );
                    @endphp

                    <select id="category_id" name="category_id">
                        <option value="">カテゴリなし</option>

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

            </div>

            <div class="card-form-actions">
                @if (request('from') === 'study')
                    <a
                        href="{{ route('study.index', request()->filled('category_id') ? ['category_id' => request('category_id')] : []) }}"
                        class="card-form-secondary"
                    >
                        戻る
                    </a>
                @else
                    <a href="{{ route('cards.index') }}" class="card-form-secondary">
                        戻る
                    </a>
                @endif

                <button type="submit" class="card-form-primary">
                    更新
                </button>
            </div>

        </form>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('input[type="file"][data-preview-target]')
                .forEach(function (input) {
                    input.addEventListener('change', function () {
                        const preview = document.getElementById(input.dataset.previewTarget);
                        const image = preview?.querySelector('img');
                        const file = input.files?.[0];

                        if (!preview || !image) {
                            return;
                        }

                        if (!file) {
                            preview.classList.remove('show');
                            image.removeAttribute('src');
                            return;
                        }

                        image.src = URL.createObjectURL(file);
                        preview.classList.add('show');

                        if (input.id === 'question_image') {
                            const remove = document.getElementById('removeQuestionImage');
                            if (remove) {
                                remove.checked = false;
                            }
                        }

                        if (input.id === 'answer_image') {
                            const remove = document.getElementById('removeAnswerImage');
                            if (remove) {
                                remove.checked = false;
                            }
                        }
                    });
                });
        });
    </script>

</body>
</html>
