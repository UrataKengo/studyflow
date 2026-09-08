<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カード登録 | StudyFlow</title>

    <link rel="stylesheet" href="{{ asset('css/cards-form.css') }}">
</head>

<body>

    <header class="card-form-topbar">
        <div class="card-form-brand">
            <a href="{{ route('dashboard.index') }}" class="card-form-logo">
                StudyFlow
            </a>

            <span class="card-form-divider"></span>

            <span class="card-form-page">
                カード登録
            </span>
        </div>

        <a href="{{ route('cards.index') }}" class="card-form-back">
            ← カード管理へ戻る
        </a>
    </header>

    <main class="card-form-content">

        <div class="card-form-heading">
            <h1>カード登録</h1>
            <p>問題と答えを入力して、新しい学習カードを作成します。</p>
        </div>

        @if ($errors->any())
            <div class="form-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form
            action="{{ route('cards.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="card-form-panel"
        >
            @csrf

            <div class="card-form-grid">

                <div class="form-section">
                    <label class="form-label" for="question">
                        問題
                    </label>

                    <textarea
                        id="question"
                        name="question"
                        placeholder="問題文を入力してください"
                    >{{ old('question') }}</textarea>

                    <span class="form-hint">
                        問題文または問題画像のどちらか一方があれば登録できます。
                    </span>
                </div>

                <div class="form-section">
                    <label class="form-label" for="question_image">
                        問題画像
                    </label>

                    <div class="image-upload-box">
                        <input
                            id="question_image"
                            type="file"
                            name="question_image"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            data-preview-target="questionImagePreview"
                        >

                        <span class="form-hint">
                            JPG / JPEG / PNG / WebP・最大5MB
                        </span>

                        <div class="image-preview" id="questionImagePreview">
                            <img src="" alt="問題画像プレビュー">
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
                    >{{ old('answer') }}</textarea>

                    <span class="form-hint">
                        解答文または解答画像のどちらか一方があれば登録できます。
                    </span>
                </div>

                <div class="form-section">
                    <label class="form-label" for="answer_image">
                        解答画像
                    </label>

                    <div class="image-upload-box">
                        <input
                            id="answer_image"
                            type="file"
                            name="answer_image"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            data-preview-target="answerImagePreview"
                        >

                        <span class="form-hint">
                            JPG / JPEG / PNG / WebP・最大5MB
                        </span>

                        <div class="image-preview" id="answerImagePreview">
                            <img src="" alt="解答画像プレビュー">
                        </div>
                    </div>
                </div>

                <div class="form-section full">
                    <label class="form-label" for="category_id">
                        カテゴリ
                    </label>

                    <select id="category_id" name="category_id">
                        <option value="">カテゴリなし</option>

                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(
                                    (string) old('category_id', request('category_id'))
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
                <a href="{{ route('cards.index') }}" class="card-form-secondary">
                    戻る
                </a>

                <button type="submit" class="card-form-primary">
                    登録
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
                    });
                });
        });
    </script>

</body>
</html>
