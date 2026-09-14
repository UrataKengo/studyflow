<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>カード管理 | StudyFlow</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}?v=20260908-unified-navigation-theme">

    <style>
        .message-fade-out {
            opacity: 0;
            transform: translateY(-4px);
            transition:
                opacity 0.35s ease,
                transform 0.35s ease;
        }


        .card-detail-image-row[hidden] {
            display: none;
        }

        .card-detail-image {
            display: block;
            max-width: 100%;
            max-height: 220px;
            width: auto;
            height: auto;
            border-radius: 10px;
            object-fit: contain;
        }

        .form-submit-disabled {
            opacity: 0.65;
            cursor: wait !important;
        }

        .card-create-modal-box {
            width: min(980px, calc(100vw - 40px));
            max-width: 980px;
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            padding: 28px;
        }

        .card-create-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 24px;
        }

        .card-create-modal-header h2 {
            margin: 0;
        }

        .card-create-modal-header p {
            margin: 8px 0 0;
            color: #6b7f93;
            font-size: 13px;
        }

        .card-create-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 22px 26px;
        }

        .card-create-grid .form-section.full {
            grid-column: 1 / -1;
        }

        .card-create-grid .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #173f68;
        }

        .card-create-grid textarea,
        .card-create-grid select,
        .card-create-grid input[type="file"] {
            width: 100%;
            box-sizing: border-box;
        }

        .card-create-grid textarea {
            min-height: 130px;
            resize: vertical;
        }

        .card-create-grid .form-hint {
            display: block;
            margin-top: 8px;
            color: #7890a8;
            font-size: 12px;
            line-height: 1.7;
        }

        .card-create-grid .image-upload-box {
            min-height: 130px;
            padding: 14px;
            border: 1px dashed #b8d5eb;
            border-radius: 12px;
            background: #fbfdff;
            box-sizing: border-box;
        }

        .card-create-grid .image-preview {
            display: none;
            margin-top: 12px;
            text-align: center;
        }

        .card-create-grid .image-preview.show {
            display: block;
        }

        .card-create-grid .image-preview img {
            display: block;
            max-width: 100%;
            max-height: 240px;
            width: auto;
            height: auto;
            margin: 0 auto;
            border-radius: 10px;
            object-fit: contain;
        }


        .edit-current-image {
            margin-bottom: 14px;
            padding: 12px;
            border: 1px solid #d6e2ec;
            border-radius: 10px;
            background: #ffffff;
        }

        .edit-current-image {
            position: relative;
        }

        .current-image-remove-btn {
            position: absolute;
            top: 38px;
            right: 18px;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: rgba(25, 39, 52, .78);
            color: #ffffff;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
        }

        .current-image-remove-btn:hover {
            background: rgba(210, 55, 65, .92);
        }

        .edit-current-image[hidden] {
            display: none;
        }

        .edit-current-image .current-image-label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
            color: #5f7690;
        }

        .edit-current-image img {
            display: block;
            max-width: 100%;
            max-height: 180px;
            width: auto;
            height: auto;
            margin: 0 auto 10px;
            border-radius: 10px;
            object-fit: contain;
        }

        .edit-current-image .remove-image {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #e24a55;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .card-create-actions {
            margin-top: 24px;
        }

        .card-form-utility-row {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            margin: -4px 0 0;
        }

        .swap-card-sides-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 8px 16px;
            border: 1px solid #b9d7ec;
            border-radius: 999px;
            background: #f7fbff;
            color: #17649a;
            font-weight: 700;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .swap-card-sides-btn:hover {
            transform: translateY(-1px);
            background: #eef8ff;
            box-shadow: 0 4px 12px rgba(31, 111, 171, .12);
        }

        .image-upload-box.is-dragover {
            border-color: #2ba7c9;
            background: #eefcff;
            box-shadow: inset 0 0 0 2px rgba(43, 167, 201, .08);
        }

        .drop-paste-hint {
            display: block;
            margin-top: 8px;
            font-size: 12px;
            color: #6f879d;
        }


        .card-create-grid .image-preview {
            position: relative;
            width: fit-content;
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .preview-remove-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 3;
            display: none;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: rgba(25, 39, 52, .78);
            color: #ffffff;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .18);
        }

        .card-create-grid .image-preview.show .preview-remove-btn {
            display: inline-flex;
        }

        .preview-remove-btn:hover {
            background: rgba(210, 55, 65, .92);
        }

        .continue-create-btn {
            border: 1px solid #8ec8ea;
            background: #ffffff;
            color: #1574b8;
        }

        .continue-create-btn:hover {
            background: #f4fbff;
        }

        @media (max-width: 760px) {
            .card-create-modal-box {
                width: calc(100vw - 24px);
                padding: 22px 18px;
            }

            .card-create-grid {
                grid-template-columns: 1fr;
            }

            .card-create-grid .form-section.full {
                grid-column: auto;
            }
        }
    </style>
</head>

<body class="cards-page">
    <div class="cards-background" aria-hidden="true">
        <span class="cards-decor-circle cards-decor-left-top"></span>
        <span class="cards-decor-circle cards-decor-left-bottom"></span>
        <span class="cards-decor-circle cards-decor-right-top"></span>
        <span class="cards-decor-circle cards-decor-right-bottom"></span>
        <span class="cards-decor-line cards-decor-line-left"></span>
        <span class="cards-decor-line cards-decor-line-right"></span>
    </div>

    <header class="card-topbar app-header">
        <a href="{{ route('dashboard.index') }}"
            class="app-brand"
            aria-label="StudyFlow ダッシュボードへ">

            <span class="app-brand-icon" aria-hidden="true">
                <svg viewBox="0 0 86 64">
                    <defs>
                        <linearGradient id="cardsBookLeft" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#2196f3" />
                            <stop offset="100%" stop-color="#2677ea" />
                        </linearGradient>

                        <linearGradient id="cardsBookRight" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#41c6cc" />
                            <stop offset="100%" stop-color="#35c783" />
                        </linearGradient>
                    </defs>

                    <path
                        d="M7 8C17 5 27 6 36 11C40 13 42 16 43 19V57C38 52 32 49 25 47C19 45 13 45 7 47V8Z"
                        fill="url(#cardsBookLeft)" />

                    <path
                        d="M79 8C69 5 59 6 50 11C46 13 44 16 43 19V57C48 52 54 49 61 47C67 45 73 45 79 47V8Z"
                        fill="url(#cardsBookRight)" />

                    <path d="M43 18V57"
                        stroke="#ffffff"
                        stroke-width="3"
                        stroke-linecap="round"
                        opacity="0.9" />
                </svg>
            </span>

            <span class="app-brand-text">
                <strong>StudyFlow</strong>
                <small>記憶を、少しずつ確かなものに。</small>
            </span>
        </a>

        <nav class="app-nav" aria-label="メインナビゲーション">
            <a href="{{ route('dashboard.index') }}"
                class="app-nav-link">
                <span class="app-nav-icon" aria-hidden="true">⌂</span>
                <span>ダッシュボード</span>
            </a>

            <a href="{{ route('cards.index') }}"
                class="app-nav-link is-active"
                aria-current="page">
                <span class="app-nav-icon" aria-hidden="true">▣</span>
                <span>カード管理</span>
            </a>

            <a href="{{ route('study.index') }}"
                class="app-nav-link">
                <span class="app-nav-icon" aria-hidden="true">▷</span>
                <span>学習開始</span>
            </a>
        </nav>

        <form action="{{ route('logout') }}"
            method="POST"
            class="header-logout-form">
            @csrf

            <button type="submit" class="header-logout-btn">
                <span aria-hidden="true">↪</span>
                ログアウト
            </button>
        </form>
    </header>

    <aside class="cards-side-copy cards-side-copy-left" aria-hidden="true">
        SMALL STEPS<br>
        BIG CHANGES
    </aside>

    <aside class="cards-side-copy cards-side-copy-right-top" aria-hidden="true">
        A Better You<br>
        One Card at a Time.
        <span></span>
    </aside>

    <aside class="cards-side-copy cards-side-copy-right-bottom" aria-hidden="true">
        学ぶことが、<br>
        きっと楽しくなる。
        <span></span>
    </aside>

    <main class="content">
        <section class="page-heading">
            <h1>カード管理</h1>

            <p>
                あなたの学習カードを管理しましょう。<br>
                追加・編集・削除、カテゴリの整理ができます。
            </p>
        </section>

        @if (session('success'))
            <div class="success-message" id="successMessage">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="error-message" id="errorMessage">
                {{ session('error') }}
            </div>
        @endif

        @php
            $selectedCategory = $categories->first(function ($category) use ($categoryId) {
                return (string) $category->id === (string) $categoryId;
            });
        @endphp

        <section class="category-section" id="categorySection" data-hover-enabled="true">
            <div class="category-bar">
                <button type="button"
                    class="category-toggle-main"
                    id="categoryToggleButton"
                    aria-expanded="false"
                    aria-controls="categoryListPanel">

                    <span class="category-bar-icon">🏷</span>

                    <span class="category-bar-text">
                        <strong>カテゴリ</strong>
                        <small>カードをカテゴリで絞り込みます</small>
                    </span>
                </button>

                <div class="category-bar-actions">
                    <button type="button"
                        class="category-create-btn"
                        onclick="openCategoryModal()">
                        ＋ カテゴリ追加
                    </button>

                    <button type="button"
                        class="category-chevron-btn"
                        id="categoryChevronButton"
                        aria-label="カテゴリ一覧を開く"
                        aria-expanded="false">
                        ▼
                    </button>
                </div>
            </div>

            @if ($selectedCategory)
                <div class="selected-category-area">
                    <div class="selected-category-status">
                        <span class="selected-category-label">絞り込み中</span>

                        <div class="selected-category-chip">
                            <span>{{ $selectedCategory->name }}</span>

                            <a href="{{ route('cards.index', !empty($keyword) ? ['keyword' => $keyword] : []) }}"
                                class="selected-category-remove"
                                aria-label="カテゴリ絞り込みを解除">
                                ×
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('cards.index', !empty($keyword) ? ['keyword' => $keyword] : []) }}"
                        class="category-clear-link">
                        絞り込みをクリア
                    </a>
                </div>
            @endif

            <div class="category-list-panel"
                id="categoryListPanel"
                hidden>

                <div class="category-list-head">
                    <span>カテゴリ名</span>
                    <span>カード数</span>
                    <span>操作</span>
                </div>

                <a href="{{ route('cards.index', !empty($keyword) ? ['keyword' => $keyword] : []) }}"
                    class="category-list-row category-all-row {{ empty($categoryId) ? 'is-active' : '' }}">
                    <span class="category-row-name">
                        すべてのカテゴリ
                    </span>

                    <span class="category-row-count">
                        全件
                    </span>

                    <span class="category-row-actions category-row-actions-empty">
                        —
                    </span>
                </a>

                @foreach ($categories as $category)
                    <div class="category-list-row {{ (string) $categoryId === (string) $category->id ? 'is-active' : '' }}">
                        <a href="{{ route('cards.index', array_filter([
                                'category_id' => $category->id,
                                'keyword' => $keyword ?? null,
                            ])) }}"
                            class="category-row-select">

                            <span class="category-row-name">
                                {{ $category->name }}

                                @if ((string) $categoryId === (string) $category->id)
                                    <span class="category-active-mark">選択中</span>
                                @endif
                            </span>

                            <span class="category-row-count">
                                {{ $category->cards_count }}
                            </span>
                        </a>

                        <div class="category-row-actions">
                            <button type="button"
                                class="category-rename-btn category-edit-btn"
                                title="カテゴリ名を変更"
                                aria-label="{{ $category->name }} の名前を変更"
                                data-category-name="{{ $category->name }}"
                                data-update-url="{{ route('categories.update', $category->id) }}">
                                ✎
                            </button>

                            <form action="{{ route('categories.destroy', $category->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="category-delete-btn"
                                    title="カテゴリを削除"
                                    aria-label="{{ $category->name }} を削除"
                                    onclick="return confirm('「{{ $category->name }}」と登録されているカードをすべて削除しますか？')">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="card-tools">
            <div class="card-tools-main">
                <form action="{{ route('cards.index') }}" method="GET" class="search-box">
                    @if (!empty($categoryId))
                        <input type="hidden" name="category_id" value="{{ $categoryId }}">
                    @endif

                    <div class="search-input-wrap">
                        <span class="search-input-icon" aria-hidden="true">⌕</span>

                        <input type="text"
                            name="keyword"
                            value="{{ $keyword ?? '' }}"
                            placeholder="キーワード検索（問題・解答で検索）">
                    </div>

                    <button type="submit">
                        検索
                    </button>

                    <a href="{{ route('cards.index', !empty($categoryId) ? ['category_id' => $categoryId] : []) }}">
                        リセット
                    </a>
                </form>

                <div class="card-tools-actions">
                    <button type="button"
                        class="selection-mode-btn toolbar-selection-btn"
                        id="selectionModeButton">
                        <span class="selection-mode-icon">☑</span>
                        カードを選択
                    </button>

                    <button type="button"
                        class="small-create-card-btn"
                        onclick="openCardModal()">
                        ＋ 新規カード
                    </button>
                </div>
            </div>

            @if ($selectedCategory || !empty($keyword))
                <div class="active-filter-bar">
                    <div class="active-filter-items">
                        <span class="active-filter-label">絞り込み中:</span>

                        @if ($selectedCategory)
                            <span class="active-filter-chip active-filter-category">
                                {{ $selectedCategory->name }}

                                <a href="{{ route('cards.index', !empty($keyword) ? ['keyword' => $keyword] : []) }}"
                                    aria-label="カテゴリ絞り込みを解除">
                                    ×
                                </a>
                            </span>
                        @endif

                        @if (!empty($keyword))
                            <span class="active-filter-chip active-filter-keyword">
                                「{{ $keyword }}」

                                <a href="{{ route('cards.index', !empty($categoryId) ? ['category_id' => $categoryId] : []) }}"
                                    aria-label="キーワード検索を解除">
                                    ×
                                </a>
                            </span>
                        @endif
                    </div>

                    <a href="{{ route('cards.index') }}" class="active-filter-clear">
                        絞り込みをクリア
                    </a>
                </div>
            @endif
        </section>

        <div class="selection-toolbar" id="selectionToolbar" hidden>
            <div class="selection-start" id="selectionStart" hidden></div>

            <div class="selection-active-bar" id="selectionActiveBar">
                <div class="selection-toolbar-left">
                    <span class="selection-status-icon" aria-hidden="true">✓</span>

                    <strong class="selection-count" id="selectionCount">
                        0件選択中
                    </strong>

                    <span class="selection-divider"></span>

                    <label class="select-all-label" for="selectAllCards">
                        <span class="select-all-fake-box" aria-hidden="true"></span>
                        <span>すべて選択</span>
                    </label>

                    <button type="button"
                        class="selection-cancel-btn"
                        id="selectionCancelButton">
                        選択を終了
                    </button>
                </div>

                <div class="selection-actions" id="selectionActions">
                    <button type="button"
                        class="selected-category-btn"
                        id="selectedCategoryButton"
                        disabled>
                        ▣ カテゴリ変更
                    </button>

                    <button type="button"
                        class="selected-delete-btn"
                        id="selectedDeleteButton"
                        disabled>
                        🗑 削除
                    </button>
                </div>
            </div>
        </div>

        <div class="table-wrap" id="cardTableWrap">
            <table class="card-table">
                <thead>
                    <tr>
                        <th class="selection-column selection-checkbox-cell">
                            <input type="checkbox"
                                id="selectAllCards"
                                class="card-checkbox"
                                aria-label="すべてのカードを選択">
                        </th>
                        <th>問題</th>
                        <th>解答</th>
                        <th>カテゴリ</th>
                        <th>状態 / レベル</th>
                        <th>学習回数</th>
                        <th>次回復習日</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($cards as $card)
                        @php
                            $levelNames = [
                                1 => '初心者',
                                2 => '学習中',
                                3 => '定着中',
                                4 => '習得',
                                5 => 'マスター',
                            ];

                            $currentLevel = max(1, min(5, (int) $card->level));

                            $reviewDate = $card->next_review_date
                                ? $card->next_review_date->copy()->startOfDay()
                                : null;

                            $daysLeft = $reviewDate
                                ? (int) now()->startOfDay()->diffInDays($reviewDate, false)
                                : null;

                            $firstCategory = $card->categories->first();
                            $cardCategoryId = $firstCategory?->id ?? '';
                            $cardCategoryName = $firstCategory?->name ?? '未分類';

                            $statusLabels = [
                                'new' => '新規',
                                'learning' => '学習中',
                                'review' => '復習',
                                'relearning' => '復習中',
                            ];

                            $statusKey = $card->status ?? 'new';
                            $statusLabel = $statusLabels[$statusKey] ?? '学習中';

                            if ($reviewDate === null) {
                                $reviewLabel = '未学習';
                                $reviewClass = 'review-unlearned';
                            } elseif ($daysLeft < 0) {
                                $reviewLabel = abs($daysLeft) . '日超過';
                                $reviewClass = 'review-overdue';
                            } elseif ($daysLeft === 0) {
                                $reviewLabel = '今日';
                                $reviewClass = 'review-today';
                            } elseif ($daysLeft === 1) {
                                $reviewLabel = '明日';
                                $reviewClass = 'review-soon';
                            } else {
                                $reviewLabel = $daysLeft . '日後';
                                $reviewClass = 'review-later';
                            }
                        @endphp

                        <tr class="card-row"
                            data-question="{{ $card->question }}"
                            data-answer="{{ $card->answer }}"
                            data-question-image="{{ $card->question_image ? asset('storage/' . $card->question_image) : '' }}"
                            data-answer-image="{{ $card->answer_image ? asset('storage/' . $card->answer_image) : '' }}"
                            data-category="{{ $cardCategoryName }}"
                            data-status="{{ $statusLabel }}"
                            data-level="Lv.{{ $currentLevel }} {{ $levelNames[$currentLevel] }}"
                            data-study-count="{{ $card->study_count }}回"
                            data-review-label="{{ $reviewLabel }}"
                            data-review-date="{{ $reviewDate ? $reviewDate->format('Y-m-d') : '未設定' }}"
                            data-edit-url="{{ route('cards.edit', [
                                'card' => $card->id,
                                'from' => 'cards',
                                'category_id' => $categoryId,
                                'keyword' => $keyword,
                            ]) }}"
                            data-update-url="{{ route('cards.update', $card->id) }}"
                            data-delete-url="{{ route('cards.destroy', $card->id) }}"
                            data-category-id="{{ $cardCategoryId }}">
                            <td class="selection-column selection-checkbox-cell">
                                <input type="checkbox"
                                    class="card-checkbox card-select-checkbox"
                                    value="{{ $card->id }}"
                                    data-delete-url="{{ route('cards.destroy', $card->id) }}"
                                    data-update-url="{{ route('cards.update', $card->id) }}"
                                    data-question="{{ $card->question }}"
                                    data-answer="{{ $card->answer }}"
                                    aria-label="{{ $card->question }} を選択">
                            </td>

                            <td class="card-question-cell">
                                {{ $card->question }}
                            </td>

                            <td class="card-answer-cell">
                                {{ $card->answer }}
                            </td>

                            <td>
                                <span class="category-badge">
                                    {{ $cardCategoryName }}
                                </span>
                            </td>

                            <td>
                                <div class="status-level-wrap">
                                    <span class="status-badge status-{{ $statusKey }}">
                                        {{ $statusLabel }}
                                    </span>

                                    <span class="level-compact">
                                        Lv.{{ $currentLevel }}
                                    </span>
                                </div>
                            </td>

                            <td>{{ $card->study_count }} 回</td>

                            <td>
                                <span class="review-badge {{ $reviewClass }}">
                                    {{ $reviewLabel }}
                                </span>

                                <div class="review-date">
                                    {{ $reviewDate ? $reviewDate->format('Y-m-d') : '—' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-table-message">
                                @if (!empty($keyword) || !empty($categoryId))
                                    <div class="empty-state">
                                        <div class="empty-state-icon">⌕</div>
                                        <h3>該当するカードがありません</h3>
                                        <p>検索条件やカテゴリを変更してみてください。</p>

                                        <a href="{{ route('cards.index') }}"
                                            class="empty-state-reset">
                                            検索条件をリセット
                                        </a>
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-state-icon">□</div>
                                        <h3>まだカードがありません</h3>
                                        <p>「＋ 新規カード」から最初のカードを作成しましょう。</p>

                                        <button type="button"
                                            class="small-create-card-btn"
                                            onclick="openCardModal()">
                                            ＋ 新規カード
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    {{-- カード詳細モーダル --}}
    <div id="cardDetailModal" class="modal-bg">
        <div class="modal-box card-detail-modal-box">
            <div class="card-detail-header">
                <h2>カード詳細</h2>

                <button type="button"
                    class="modal-close-icon"
                    id="cardDetailCloseButton"
                    aria-label="カード詳細を閉じる">
                    ×
                </button>
            </div>

            <dl class="card-detail-list">
                <div>
                    <dt>問題</dt>
                    <dd id="detailQuestion"></dd>
                </div>

                <div id="detailQuestionImageRow" hidden>
                    <dt>問題画像</dt>
                    <dd>
                        <img id="detailQuestionImage"
                            class="card-detail-image"
                            src=""
                            alt="問題画像">
                    </dd>
                </div>

                <div>
                    <dt>解答</dt>
                    <dd id="detailAnswer"></dd>
                </div>

                <div id="detailAnswerImageRow" hidden>
                    <dt>解答画像</dt>
                    <dd>
                        <img id="detailAnswerImage"
                            class="card-detail-image"
                            src=""
                            alt="解答画像">
                    </dd>
                </div>

                <div>
                    <dt>カテゴリ</dt>
                    <dd><span class="category-badge" id="detailCategory"></span></dd>
                </div>

                <div>
                    <dt>状態 / レベル</dt>
                    <dd id="detailStatusLevel"></dd>
                </div>

                <div>
                    <dt>学習回数</dt>
                    <dd id="detailStudyCount"></dd>
                </div>

                <div>
                    <dt>次回復習日</dt>
                    <dd id="detailReview"></dd>
                </div>
            </dl>

            <div class="modal-actions card-detail-actions">
                <button type="button"
                    class="detail-delete-btn"
                    id="cardDetailDeleteButton">
                    🗑 削除
                </button>

                <div class="card-detail-actions-right">
                    <button type="button"
                        class="btn-primary detail-edit-btn"
                        id="cardDetailEditButton">
                        ✎ 編集する
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 新規カード作成モーダル --}}
    <div id="cardCreateModal" class="modal-bg">
        <div class="modal-box card-create-modal-box">
            <div class="card-create-modal-header">
                <div>
                    <h2>新規カード作成</h2>
                    <p>問題・答え・画像・カテゴリを登録できます。</p>
                </div>

                <button type="button"
                    class="modal-close-icon"
                    onclick="closeCardModal()"
                    aria-label="新規カード作成を閉じる">
                    ×
                </button>
            </div>

            <form
                action="{{ route('cards.store') }}"
                method="POST"
                enctype="multipart/form-data"
                id="cardCreateForm"
            >
                @csrf

                <input type="hidden" name="return_category_id" value="{{ $categoryId ?? '' }}">
                <input type="hidden" name="return_keyword" value="{{ $keyword ?? '' }}">
                <input type="hidden" name="continue_create" id="continueCreateInput" value="0">

                <div class="card-create-grid">

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
                            問題文または問題画像のどちらか一方があれば保存できます。
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
                                data-preview-target="createQuestionImagePreview"
                            >

                            <span class="form-hint">
                                JPG・PNG・WEBPに対応しています。最大5MB
                            </span>
                            <span class="drop-paste-hint">
                                画像をここへドラッグ＆ドロップ、または貼り付け（Ctrl+V）できます。
                            </span>

                            <div class="image-preview" id="createQuestionImagePreview">
                                <img src="" alt="問題画像プレビュー">
                                <button type="button"
                                    class="preview-remove-btn"
                                    id="clearCreateQuestionImage"
                                    aria-label="選択した画像を取り消す"
                                    title="画像を取り消す">
                                    ×
                                </button>
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
                            解答文または解答画像のどちらか一方があれば保存できます。
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
                                data-preview-target="createAnswerImagePreview"
                            >

                            <span class="form-hint">
                                JPG・PNG・WEBPに対応しています。最大5MB
                            </span>
                            <span class="drop-paste-hint">
                                画像をここへドラッグ＆ドロップ、または貼り付け（Ctrl+V）できます。
                            </span>

                            <div class="image-preview" id="createAnswerImagePreview">
                                <img src="" alt="解答画像プレビュー">
                                <button type="button"
                                    class="preview-remove-btn"
                                    id="clearCreateAnswerImage"
                                    aria-label="選択した画像を取り消す"
                                    title="画像を取り消す">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-form-utility-row">
                        <button type="button"
                            class="swap-card-sides-btn"
                            id="createSwapButton">
                            ⇄ 問題と答えを入れ替える
                        </button>
                    </div>

                    <div class="form-section full">
                        <label class="form-label" for="category_id">
                            カテゴリ
                        </label>

                        <select id="category_id" name="category_id">
                            <option value="">カテゴリなし</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @selected(
                                        (string) old('category_id', $categoryId ?? '')
                                        ===
                                        (string) $category->id
                                    )>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-actions card-create-actions">
                    

                    <button type="submit"
                        class="btn-secondary continue-create-btn"
                        id="continueCreateButton">
                        登録して次へ
                    </button>

                    <button type="submit"
                        class="btn-primary"
                        id="createSubmitButton">
                        登録する
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- カード編集モーダル --}}
    <div id="cardEditModal" class="modal-bg">
        <div class="modal-box card-create-modal-box">
            <div class="card-create-modal-header">
                <div>
                    <h2>カード編集</h2>
                    <p>問題・答え・画像・カテゴリを編集できます。</p>
                </div>

                <button type="button"
                    class="modal-close-icon"
                    onclick="closeEditCardModal()"
                    aria-label="カード編集を閉じる">
                    ×
                </button>
            </div>

            <form
                id="cardEditForm"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <input type="hidden" name="return_category_id" value="{{ $categoryId ?? '' }}">
                <input type="hidden" name="return_keyword" value="{{ $keyword ?? '' }}">

                <div class="card-create-grid">

                    <div class="form-section">
                        <label class="form-label" for="edit_question">
                            問題
                        </label>

                        <textarea
                            id="edit_question"
                            name="question"
                            placeholder="問題文を入力してください"
                        ></textarea>

                        <span class="form-hint">
                            問題文または問題画像のどちらか一方があれば保存できます。
                        </span>
                    </div>

                    <div class="form-section">
                        <label class="form-label" for="edit_question_image">
                            問題画像
                        </label>

                        <div class="image-upload-box">
                            <div class="current-image edit-current-image"
                                id="editCurrentQuestionImage"
                                hidden>
                                <span class="current-image-label">現在の画像</span>

                                <img
                                    id="editCurrentQuestionImageImg"
                                    src=""
                                    alt="現在の問題画像"
                                >
                                <button type="button"
                                    class="current-image-remove-btn"
                                    id="removeCurrentQuestionImageButton"
                                    aria-label="現在の問題画像を削除"
                                    title="現在の画像を削除">
                                    ×
                                </button>

                                <input type="hidden"
                                    name="remove_question_image"
                                    id="editRemoveQuestionImage"
                                    value="0">

                                
                            </div>

                            <input
                                id="edit_question_image"
                                type="file"
                                name="question_image"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                data-preview-target="editQuestionImagePreview"
                            >

                            <span class="form-hint">
                                新しい画像を選ぶと現在の画像を置き換えます。最大5MB
                            </span>
                            <span class="drop-paste-hint">
                                画像をここへドラッグ＆ドロップ、または貼り付け（Ctrl+V）できます。
                            </span>

                            <div class="image-preview" id="editQuestionImagePreview">
                                <img src="" alt="新しい問題画像プレビュー">
                                <button type="button"
                                    class="preview-remove-btn"
                                    id="clearEditQuestionImage"
                                    aria-label="選択した画像を取り消す"
                                    title="画像を取り消す">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="form-label" for="edit_answer">
                            答え
                        </label>

                        <textarea
                            id="edit_answer"
                            name="answer"
                            placeholder="答えを入力してください"
                        ></textarea>

                        <span class="form-hint">
                            解答文または解答画像のどちらか一方があれば保存できます。
                        </span>
                    </div>

                    <div class="form-section">
                        <label class="form-label" for="edit_answer_image">
                            解答画像
                        </label>

                        <div class="image-upload-box">
                            <div class="current-image edit-current-image"
                                id="editCurrentAnswerImage"
                                hidden>
                                <span class="current-image-label">現在の画像</span>

                                <img
                                    id="editCurrentAnswerImageImg"
                                    src=""
                                    alt="現在の解答画像"
                                >
                                <button type="button"
                                    class="current-image-remove-btn"
                                    id="removeCurrentAnswerImageButton"
                                    aria-label="現在の解答画像を削除"
                                    title="現在の画像を削除">
                                    ×
                                </button>

                                <input type="hidden"
                                    name="remove_answer_image"
                                    id="editRemoveAnswerImage"
                                    value="0">

                                
                            </div>

                            <input
                                id="edit_answer_image"
                                type="file"
                                name="answer_image"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                data-preview-target="editAnswerImagePreview"
                            >

                            <span class="form-hint">
                                新しい画像を選ぶと現在の画像を置き換えます。最大5MB
                            </span>
                            <span class="drop-paste-hint">
                                画像をここへドラッグ＆ドロップ、または貼り付け（Ctrl+V）できます。
                            </span>

                            <div class="image-preview" id="editAnswerImagePreview">
                                <img src="" alt="新しい解答画像プレビュー">
                                <button type="button"
                                    class="preview-remove-btn"
                                    id="clearEditAnswerImage"
                                    aria-label="選択した画像を取り消す"
                                    title="画像を取り消す">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-form-utility-row">
                        <button type="button"
                            class="swap-card-sides-btn"
                            id="editSwapButton">
                            ⇄ 問題と答えを入れ替える
                        </button>
                    </div>

                    <div class="form-section full">
                        <label class="form-label" for="edit_category_id">
                            カテゴリ
                        </label>

                        <select id="edit_category_id" name="category_id">
                            <option value="">カテゴリなし</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-actions card-create-actions">
                    

                    <button type="submit" class="btn-primary">
                        更新する
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- カテゴリ追加モーダル --}}
    <div id="categoryCreateModal" class="modal-bg">
        <div class="modal-box">
            <h2>カテゴリ追加</h2>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="category_name">カテゴリ名</label>
                    <input id="category_name" type="text" name="name" required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeCategoryModal()">
                        キャンセル
                    </button>

                    <button type="submit" class="btn-primary">
                        登録する
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- カテゴリ名変更モーダル --}}
    <div id="categoryEditModal" class="modal-bg">
        <div class="modal-box">
            <h2>カテゴリ名変更</h2>

            <form id="categoryEditForm" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="edit_category_name">カテゴリ名</label>
                    <input id="edit_category_name"
                        type="text"
                        name="name"
                        required>
                </div>

                <div class="modal-actions">
                    <button type="button"
                        class="btn-secondary"
                        onclick="closeEditCategoryModal()">
                        キャンセル
                    </button>

                    <button type="submit" class="btn-primary">
                        更新する
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 選択カードのカテゴリ一括変更モーダル --}}
    <div id="bulkCategoryModal" class="modal-bg">
        <div class="modal-box bulk-category-modal-box">
            <h2>カテゴリを変更</h2>

            <p class="bulk-category-description">
                選択したカードのカテゴリをまとめて変更します。
            </p>

            <div class="form-group">
                <label for="bulk_category_id">変更先カテゴリ</label>

                <select id="bulk_category_id">
                    <option value="">カテゴリなし</option>

                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="modal-actions">
                <button type="button"
                    class="btn-secondary"
                    id="bulkCategoryCancelButton">
                    キャンセル
                </button>

                <button type="button"
                    class="btn-primary"
                    id="bulkCategoryApplyButton">
                    変更する
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cardModal = document.getElementById('cardCreateModal');
            const editCardModal = document.getElementById('cardEditModal');
            const categoryModal = document.getElementById('categoryCreateModal');
            const editCategoryModal = document.getElementById('categoryEditModal');
            const bulkCategoryModal = document.getElementById('bulkCategoryModal');
            const cardDetailModal = document.getElementById('cardDetailModal');

            const categoryEditForm = document.getElementById('categoryEditForm');
            const editCategoryName = document.getElementById('edit_category_name');

            const detailQuestion = document.getElementById('detailQuestion');
            const detailAnswer = document.getElementById('detailAnswer');
            const detailQuestionImageRow = document.getElementById('detailQuestionImageRow');
            const detailQuestionImage = document.getElementById('detailQuestionImage');
            const detailAnswerImageRow = document.getElementById('detailAnswerImageRow');
            const detailAnswerImage = document.getElementById('detailAnswerImage');
            const detailCategory = document.getElementById('detailCategory');
            const detailStatusLevel = document.getElementById('detailStatusLevel');
            const detailStudyCount = document.getElementById('detailStudyCount');
            const detailReview = document.getElementById('detailReview');
            const cardDetailCloseButton = document.getElementById('cardDetailCloseButton');
            const cardDetailEditButton = document.getElementById('cardDetailEditButton');
            const editCardForm = document.getElementById('cardEditForm');
            const editQuestion = document.getElementById('edit_question');
            const editAnswer = document.getElementById('edit_answer');
            const editCategory = document.getElementById('edit_category_id');
            const editQuestionImage = document.getElementById('edit_question_image');
            const editAnswerImage = document.getElementById('edit_answer_image');
            const editCurrentQuestionImage = document.getElementById('editCurrentQuestionImage');
            const editCurrentQuestionImageImg = document.getElementById('editCurrentQuestionImageImg');
            const editCurrentAnswerImage = document.getElementById('editCurrentAnswerImage');
            const editCurrentAnswerImageImg = document.getElementById('editCurrentAnswerImageImg');
            const editRemoveQuestionImage = document.getElementById('editRemoveQuestionImage');
            const editRemoveAnswerImage = document.getElementById('editRemoveAnswerImage');
            const removeCurrentQuestionImageButton = document.getElementById('removeCurrentQuestionImageButton');
            const removeCurrentAnswerImageButton = document.getElementById('removeCurrentAnswerImageButton');

            let currentDetailCard = null;


            /*
             * STEP 5-4：操作性の仕上げ
             * ・成功/エラーメッセージを自動で消す
             * ・モーダルを開いたら最初の入力欄へ自動フォーカス
             * ・フォーム送信時の連打を防止
             */

            function autoHideMessage(element, delay = 3000) {
                if (!element) {
                    return;
                }

                window.setTimeout(function () {
                    element.classList.add('message-fade-out');

                    window.setTimeout(function () {
                        element.remove();
                    }, 350);
                }, delay);
            }

            autoHideMessage(document.getElementById('successMessage'), 3000);
            autoHideMessage(document.getElementById('errorMessage'), 5000);

            function focusFirstField(modal, selector) {
                window.setTimeout(function () {
                    const field = modal.querySelector(selector);

                    if (field) {
                        field.focus();

                        if (
                            typeof field.select === 'function' &&
                            field.tagName !== 'SELECT'
                        ) {
                            field.select();
                        }
                    }
                }, 50);
            }

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const submitButton = form.querySelector(
                        'button[type="submit"], input[type="submit"]'
                    );

                    if (!submitButton || submitButton.dataset.submitting === '1') {
                        return;
                    }

                    submitButton.dataset.submitting = '1';
                    submitButton.disabled = true;
                    submitButton.classList.add('form-submit-disabled');

                    if (submitButton.tagName === 'BUTTON') {
                        submitButton.dataset.originalText =
                            submitButton.textContent;

                        submitButton.textContent = '処理中...';
                    }
                });
            });

            window.openCardModal = function () {
                cardModal.classList.add('show');
                focusFirstField(cardModal, '#question');
            };

            window.closeCardModal = function () {
                cardModal.classList.remove('show');
            };

            function resetPreview(previewId) {
                const preview = document.getElementById(previewId);

                if (!preview) {
                    return;
                }

                const image = preview.querySelector('img');

                preview.classList.remove('show');

                if (image) {
                    image.removeAttribute('src');
                }
            }

            function setCurrentImage(wrapper, image, url) {
                if (!wrapper || !image) {
                    return;
                }

                if (url) {
                    image.src = url;
                    wrapper.hidden = false;
                } else {
                    image.removeAttribute('src');
                    wrapper.hidden = true;
                }
            }

            window.openEditCardModal = function (row) {
                if (!row) {
                    return;
                }

                editQuestion.value = row.dataset.question ?? '';
                editAnswer.value = row.dataset.answer ?? '';
                editCategory.value = row.dataset.categoryId ?? '';
                editCardForm.action = row.dataset.updateUrl;

                editQuestionImage.value = '';
                editAnswerImage.value = '';
                editRemoveQuestionImage.value = '0';
                editRemoveAnswerImage.value = '0';

                resetPreview('editQuestionImagePreview');
                resetPreview('editAnswerImagePreview');

                setCurrentImage(
                    editCurrentQuestionImage,
                    editCurrentQuestionImageImg,
                    row.dataset.questionImage ?? ''
                );

                setCurrentImage(
                    editCurrentAnswerImage,
                    editCurrentAnswerImageImg,
                    row.dataset.answerImage ?? ''
                );

                editCardModal.classList.add('show');
                focusFirstField(editCardModal, '#edit_question');
            };

            window.closeEditCardModal = function () {
                editCardModal.classList.remove('show');
            };

            if (removeCurrentQuestionImageButton) {
                removeCurrentQuestionImageButton.addEventListener('click', function () {
                    editRemoveQuestionImage.value = '1';
                    editCurrentQuestionImage.hidden = true;
                    editCurrentQuestionImageImg.removeAttribute('src');
                });
            }

            if (removeCurrentAnswerImageButton) {
                removeCurrentAnswerImageButton.addEventListener('click', function () {
                    editRemoveAnswerImage.value = '1';
                    editCurrentAnswerImage.hidden = true;
                    editCurrentAnswerImageImg.removeAttribute('src');
                });
            }


            document
                .querySelectorAll(
                    '#cardCreateModal input[type="file"][data-preview-target], ' +
                    '#cardEditModal input[type="file"][data-preview-target]'
                )
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

            window.openCategoryModal = function () {
                categoryModal.classList.add('show');
                focusFirstField(categoryModal, '#category_name');
            };

            window.closeCategoryModal = function () {
                categoryModal.classList.remove('show');
            };

            window.openEditCategoryModal = function (
                categoryName,
                updateUrl
            ) {
                editCategoryName.value = categoryName ?? '';
                categoryEditForm.action = updateUrl;
                editCategoryModal.classList.add('show');
                focusFirstField(editCategoryModal, '#edit_category_name');
            };

            window.closeEditCategoryModal = function () {
                editCategoryModal.classList.remove('show');
            };

            function setDetailImage(rowElement, imageElement, imageUrl) {
                if (!rowElement || !imageElement) {
                    return;
                }

                if (imageUrl) {
                    imageElement.src = imageUrl;
                    rowElement.hidden = false;
                } else {
                    imageElement.removeAttribute('src');
                    rowElement.hidden = true;
                }
            }

            function openCardDetail(row) {
                currentDetailCard = row;

                detailQuestion.textContent = row.dataset.question ?? '';
                detailAnswer.textContent = row.dataset.answer ?? '';

                setDetailImage(
                    detailQuestionImageRow,
                    detailQuestionImage,
                    row.dataset.questionImage ?? ''
                );

                setDetailImage(
                    detailAnswerImageRow,
                    detailAnswerImage,
                    row.dataset.answerImage ?? ''
                );
                detailCategory.textContent = row.dataset.category ?? '未分類';
                detailStatusLevel.textContent =
                    (row.dataset.status ?? '') + ' / ' + (row.dataset.level ?? '');
                detailStudyCount.textContent = row.dataset.studyCount ?? '0回';
                detailReview.textContent =
                    (row.dataset.reviewLabel ?? '') +
                    '（' + (row.dataset.reviewDate ?? '未設定') + '）';

                cardDetailModal.classList.add('show');
            }

            function closeCardDetail() {
                cardDetailModal.classList.remove('show');
                currentDetailCard = null;
            }

            cardDetailCloseButton.addEventListener('click', closeCardDetail);

            cardDetailEditButton.addEventListener('click', function () {
                if (!currentDetailCard) {
                    return;
                }

                const detailCard = currentDetailCard;

                closeCardDetail();
                openEditCardModal(detailCard);
            });

            cardDetailDeleteButton.addEventListener('click', async function () {
                if (!currentDetailCard) {
                    return;
                }

                const question = currentDetailCard.dataset.question ?? 'このカード';

                if (!confirm('「' + question + '」を削除しますか？\nこの操作は元に戻せません。')) {
                    return;
                }

                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content');

                cardDetailDeleteButton.disabled = true;
                cardDetailDeleteButton.textContent = '削除中...';

                try {
                    const response = await fetch(
                        currentDetailCard.dataset.deleteUrl,
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type':
                                    'application/x-www-form-urlencoded;charset=UTF-8',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new URLSearchParams({
                                _token: csrfToken,
                                _method: 'DELETE',
                                return_category_id: @json($categoryId ?? ''),
                                return_keyword: @json($keyword ?? '')
                            })
                        }
                    );

                    if (!response.ok) {
                        throw new Error('カードの削除に失敗しました。');
                    }

                    window.location.reload();

                } catch (error) {
                    alert('カードを削除できませんでした。\nページを更新して確認してください。');

                    cardDetailDeleteButton.disabled = false;
                    cardDetailDeleteButton.textContent = '🗑 削除';
                }
            });

            function closeAllMenus() {
                document
                    .querySelectorAll('.option-menu')
                    .forEach(function (menu) {
                        menu.classList.remove('show');
                    });

                document
                    .querySelectorAll('.option-btn')
                    .forEach(function (button) {
                        button.setAttribute('aria-expanded', 'false');
                    });
            }

            function setupOptionMenu(buttonSelector, wrapperSelector, menuSelector) {
                document.querySelectorAll(buttonSelector).forEach(function (button) {
                    button.addEventListener('click', function (event) {
                        event.stopPropagation();

                        const menu = button
                            .closest(wrapperSelector)
                            .querySelector(menuSelector);

                        const shouldOpen = !menu.classList.contains('show');

                        closeAllMenus();

                        if (shouldOpen) {
                            menu.classList.add('show');
                            button.setAttribute('aria-expanded', 'true');
                        }
                    });
                });

                document.querySelectorAll(menuSelector).forEach(function (menu) {
                    menu.addEventListener('click', function (event) {
                        event.stopPropagation();
                    });
                });
            }

            setupOptionMenu(
                '.option-btn',
                '.option-wrapper',
                '.option-menu'
            );

            document
                .querySelectorAll('.category-edit-btn')
                .forEach(function (button) {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        openEditCategoryModal(
                            button.dataset.categoryName,
                            button.dataset.updateUrl
                        );
                    });
                });

            const categoryToggleButton =
                document.getElementById('categoryToggleButton');

            const categoryChevronButton =
                document.getElementById('categoryChevronButton');

            const categoryListPanel =
                document.getElementById('categoryListPanel');

            const categorySection =
                document.getElementById('categorySection');

            const hasSelectedCategory = @json(!empty($categoryId));
            const hoverMedia = window.matchMedia('(hover: hover) and (pointer: fine)');

            let categoryPinnedOpen = false;
            let categoryHoverOpen = false;
            let categoryCloseTimer = null;

            function isDesktopHoverMode() {
                return hoverMedia.matches;
            }

            function renderCategoryPanel() {
                const open = categoryPinnedOpen || categoryHoverOpen;

                categoryListPanel.hidden = !open;

                categoryToggleButton.setAttribute(
                    'aria-expanded',
                    open ? 'true' : 'false'
                );

                categoryChevronButton.setAttribute(
                    'aria-expanded',
                    open ? 'true' : 'false'
                );

                categoryChevronButton.textContent =
                    categoryPinnedOpen ? '▲' : (open ? '▲' : '▼');

                categoryChevronButton.setAttribute(
                    'aria-label',
                    categoryPinnedOpen
                        ? 'カテゴリ一覧の固定を解除'
                        : (open ? 'カテゴリ一覧を閉じる' : 'カテゴリ一覧を開く')
                );

                categorySection.classList.toggle(
                    'is-category-open',
                    open
                );

                categorySection.classList.toggle(
                    'is-category-pinned',
                    categoryPinnedOpen
                );
            }

            function openCategoryByHover() {
                if (!isDesktopHoverMode() || categoryPinnedOpen) {
                    return;
                }

                if (categoryCloseTimer) {
                    clearTimeout(categoryCloseTimer);
                    categoryCloseTimer = null;
                }

                categoryHoverOpen = true;
                renderCategoryPanel();
            }

            function scheduleCategoryHoverClose() {
                if (!isDesktopHoverMode() || categoryPinnedOpen) {
                    return;
                }

                if (categoryCloseTimer) {
                    clearTimeout(categoryCloseTimer);
                }

                categoryCloseTimer = window.setTimeout(function () {
                    categoryHoverOpen = false;
                    renderCategoryPanel();
                }, 120);
            }

            function toggleCategoryPinned() {
                categoryPinnedOpen = !categoryPinnedOpen;
                categoryHoverOpen = false;
                renderCategoryPanel();
            }

            /*
             * 初期状態
             * ・PC: 閉じた状態。ホバーで一時表示
             * ・クリック: 開いた状態を固定
             * ・スマホ/タブレット: タップで開閉
             * ・選択中カテゴリはチップのみ常時表示
             */
            categoryPinnedOpen = false;
            categoryHoverOpen = false;
            renderCategoryPanel();

            categorySection.addEventListener('mouseenter', function () {
                openCategoryByHover();
            });

            categorySection.addEventListener('mouseleave', function () {
                scheduleCategoryHoverClose();
            });

            categoryListPanel.addEventListener('mouseenter', function () {
                openCategoryByHover();
            });

            categoryListPanel.addEventListener('mouseleave', function () {
                scheduleCategoryHoverClose();
            });

            categoryToggleButton.addEventListener('click', function () {
                if (isDesktopHoverMode()) {
                    toggleCategoryPinned();
                    return;
                }

                categoryPinnedOpen = !categoryPinnedOpen;
                renderCategoryPanel();
            });

            categoryChevronButton.addEventListener('click', function (event) {
                event.stopPropagation();

                categoryPinnedOpen = !categoryPinnedOpen;
                categoryHoverOpen = false;
                renderCategoryPanel();
            });

            hoverMedia.addEventListener('change', function () {
                categoryHoverOpen = false;
                categoryPinnedOpen = false;
                renderCategoryPanel();
            });

            document.addEventListener('click', closeAllMenus);

            [
                cardModal,
                editCardModal,
                categoryModal,
                editCategoryModal,
                bulkCategoryModal,
                cardDetailModal
            ].forEach(function (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.classList.remove('show');
                    }
                });
            });

            const selectionToolbar = document.getElementById('selectionToolbar');
            const selectionModeButton = document.getElementById('selectionModeButton');
            const selectionStart = document.getElementById('selectionStart');
            const selectionActiveBar = document.getElementById('selectionActiveBar');
            const selectionCancelButton = document.getElementById('selectionCancelButton');
            const selectedDeleteButton = document.getElementById('selectedDeleteButton');
            const selectedCategoryButton = document.getElementById('selectedCategoryButton');
            const selectionCount = document.getElementById('selectionCount');
            const selectAllCards = document.getElementById('selectAllCards');
            const bulkCategoryCancelButton = document.getElementById('bulkCategoryCancelButton');
            const bulkCategoryApplyButton = document.getElementById('bulkCategoryApplyButton');
            const bulkCategorySelect = document.getElementById('bulk_category_id');

            const cardCheckboxes = Array.from(
                document.querySelectorAll('.card-select-checkbox')
            );

            let selectionMode = false;

            function selectedCheckboxes() {
                return cardCheckboxes.filter(function (checkbox) {
                    return checkbox.checked;
                });
            }

            function updateSelectionState() {
                const selected = selectedCheckboxes();
                const selectedCount = selected.length;
                const hasSelection = selectedCount > 0;

                selectionCount.textContent = selectedCount + '件選択中';
                selectedDeleteButton.disabled = !hasSelection;
                selectedCategoryButton.disabled = !hasSelection;

                cardCheckboxes.forEach(function (checkbox) {
                    const row = checkbox.closest('.card-row');

                    if (row) {
                        row.classList.toggle('is-selected', checkbox.checked);
                    }
                });

                if (selectAllCards) {
                    const allSelected =
                        cardCheckboxes.length > 0 &&
                        selectedCount === cardCheckboxes.length;

                    selectAllCards.checked = allSelected;
                    selectAllCards.indeterminate =
                        selectedCount > 0 && !allSelected;
                }

                document.body.classList.toggle(
                    'has-card-selection',
                    selectionMode && hasSelection
                );
            }

            function enterSelectionMode() {
                selectionMode = true;
                document.body.classList.add('selection-mode');
                selectionToolbar.hidden = false;
                selectionStart.hidden = true;
                selectionActiveBar.hidden = false;
                closeAllMenus();
                updateSelectionState();
            }

            function exitSelectionMode() {
                selectionMode = false;
                document.body.classList.remove(
                    'selection-mode',
                    'has-card-selection'
                );

                selectionToolbar.hidden = true;
                selectionStart.hidden = true;
                selectionActiveBar.hidden = false;

                cardCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = false;
                });

                if (selectAllCards) {
                    selectAllCards.checked = false;
                    selectAllCards.indeterminate = false;
                }

                updateSelectionState();
            }

            selectionModeButton.addEventListener(
                'click',
                enterSelectionMode
            );

            selectionCancelButton.addEventListener(
                'click',
                exitSelectionMode
            );

            cardCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener(
                    'change',
                    updateSelectionState
                );

                const row = checkbox.closest('.card-row');

                if (row) {
                    row.addEventListener('click', function (event) {
                        if (
                            event.target.closest(
                                'input, button, a, form, .option-wrapper'
                            )
                        ) {
                            return;
                        }

                        if (selectionMode) {
                            checkbox.checked = !checkbox.checked;
                            updateSelectionState();
                            return;
                        }

                        openCardDetail(row);
                    });
                }
            });

            if (selectAllCards) {
                selectAllCards.addEventListener('change', function () {
                    cardCheckboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAllCards.checked;
                    });

                    updateSelectionState();
                });
            }

            selectedCategoryButton.addEventListener('click', function () {
                if (selectedCheckboxes().length === 0) {
                    return;
                }

                bulkCategoryModal.classList.add('show');
                bulkCategorySelect.focus();
            });

            bulkCategoryCancelButton.addEventListener('click', function () {
                bulkCategoryModal.classList.remove('show');
            });

            bulkCategoryApplyButton.addEventListener('click', async function () {
                const selected = selectedCheckboxes();

                if (selected.length === 0) {
                    bulkCategoryModal.classList.remove('show');
                    cardDetailModal.classList.remove('show');
                    return;
                }

                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content');

                bulkCategoryApplyButton.disabled = true;
                bulkCategoryApplyButton.textContent = '変更中...';

                try {
                    for (const checkbox of selected) {
                        const response = await fetch(
                            checkbox.dataset.updateUrl,
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type':
                                        'application/x-www-form-urlencoded;charset=UTF-8',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: new URLSearchParams({
                                    _token: csrfToken,
                                    _method: 'PUT',
                                    question: checkbox.dataset.question,
                                    answer: checkbox.dataset.answer,
                                    category_id: bulkCategorySelect.value,
                                    return_category_id: @json($categoryId ?? ''),
                                    return_keyword: @json($keyword ?? '')
                                })
                            }
                        );

                        if (!response.ok) {
                            throw new Error('カテゴリ変更に失敗しました。');
                        }
                    }

                    window.location.reload();

                } catch (error) {
                    alert(
                        '一部のカードのカテゴリを変更できませんでした。\n' +
                        'ページを更新して確認してください。'
                    );

                    bulkCategoryApplyButton.disabled = false;
                    bulkCategoryApplyButton.textContent = '変更する';
                }
            });

            selectedDeleteButton.addEventListener('click', async function () {
                const selected = selectedCheckboxes();

                if (selected.length === 0) {
                    return;
                }

                const message =
                    selected.length +
                    '件のカードを削除します。\nこの操作は元に戻せません。よろしいですか？';

                if (!confirm(message)) {
                    return;
                }

                const csrfToken = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content');

                selectedDeleteButton.disabled = true;
                selectedDeleteButton.textContent = '削除中...';

                try {
                    for (const checkbox of selected) {
                        const response = await fetch(
                            checkbox.dataset.deleteUrl,
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type':
                                        'application/x-www-form-urlencoded;charset=UTF-8',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: new URLSearchParams({
                                    _token: csrfToken,
                                    _method: 'DELETE',
                                    return_category_id: @json($categoryId ?? ''),
                                    return_keyword: @json($keyword ?? '')
                                })
                            }
                        );

                        if (!response.ok) {
                            throw new Error('カードの削除に失敗しました。');
                        }
                    }

                    window.location.reload();

                } catch (error) {
                    alert(
                        '一部のカードを削除できませんでした。\nページを更新して確認してください。'
                    );

                    selectedDeleteButton.disabled = false;
                    selectedDeleteButton.textContent = '🗑 削除';
                }
            });

            updateSelectionState();

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAllMenus();

                    cardModal.classList.remove('show');
                    editCardModal.classList.remove('show');
                    categoryModal.classList.remove('show');
                    editCategoryModal.classList.remove('show');
                    bulkCategoryModal.classList.remove('show');

                    if (selectionMode) {
                        exitSelectionMode();
                    }
                }
            });

            function transferFileToInput(input, file) {
                if (!input || !file || !file.type.startsWith('image/')) {
                    return;
                }

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }

            function clearSelectedImage(input, previewId) {
                if (!input) {
                    return;
                }

                input.value = '';
                resetPreview(previewId);
            }

            [
                ['clearCreateQuestionImage', 'question_image', 'createQuestionImagePreview'],
                ['clearCreateAnswerImage', 'answer_image', 'createAnswerImagePreview'],
                ['clearEditQuestionImage', 'edit_question_image', 'editQuestionImagePreview'],
                ['clearEditAnswerImage', 'edit_answer_image', 'editAnswerImagePreview']
            ].forEach(function (config) {
                const button = document.getElementById(config[0]);
                const input = document.getElementById(config[1]);

                if (!button || !input) {
                    return;
                }

                button.addEventListener('click', function () {
                    clearSelectedImage(input, config[2]);
                });
            });

            function setupDropPasteZone(input) {
                if (!input) {
                    return;
                }

                const box = input.closest('.image-upload-box');

                if (!box) {
                    return;
                }

                ['dragenter', 'dragover'].forEach(function (eventName) {
                    box.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        box.classList.add('is-dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function (eventName) {
                    box.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                        box.classList.remove('is-dragover');
                    });
                });

                box.addEventListener('drop', function (event) {
                    const file = Array.from(event.dataTransfer?.files ?? [])
                        .find(function (item) {
                            return item.type.startsWith('image/');
                        });

                    if (file) {
                        transferFileToInput(input, file);
                    }
                });

                box.addEventListener('paste', function (event) {
                    const items = Array.from(event.clipboardData?.items ?? []);
                    const imageItem = items.find(function (item) {
                        return item.type.startsWith('image/');
                    });

                    if (!imageItem) {
                        return;
                    }

                    event.preventDefault();

                    const file = imageItem.getAsFile();

                    if (file) {
                        transferFileToInput(input, file);
                    }
                });

                box.setAttribute('tabindex', '0');
            }

            [
                document.getElementById('question_image'),
                document.getElementById('answer_image'),
                document.getElementById('edit_question_image'),
                document.getElementById('edit_answer_image')
            ].forEach(setupDropPasteZone);

            function swapTextValues(first, second) {
                const firstValue = first.value;
                first.value = second.value;
                second.value = firstValue;
            }

            function swapInputFiles(firstInput, secondInput) {
                const firstFile = firstInput?.files?.[0] ?? null;
                const secondFile = secondInput?.files?.[0] ?? null;

                const firstTransfer = new DataTransfer();
                const secondTransfer = new DataTransfer();

                if (secondFile) {
                    firstTransfer.items.add(secondFile);
                }

                if (firstFile) {
                    secondTransfer.items.add(firstFile);
                }

                if (firstInput) {
                    firstInput.files = firstTransfer.files;
                    firstInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (secondInput) {
                    secondInput.files = secondTransfer.files;
                    secondInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }

            const createSwapButton = document.getElementById('createSwapButton');

            if (createSwapButton) {
                createSwapButton.addEventListener('click', function () {
                    const question = document.getElementById('question');
                    const answer = document.getElementById('answer');
                    const questionImage = document.getElementById('question_image');
                    const answerImage = document.getElementById('answer_image');

                    swapTextValues(question, answer);
                    swapInputFiles(questionImage, answerImage);
                });
            }

            const editSwapButton = document.getElementById('editSwapButton');

            if (editSwapButton) {
                editSwapButton.addEventListener('click', function () {
                    swapTextValues(editQuestion, editAnswer);
                    swapInputFiles(editQuestionImage, editAnswerImage);

                    const questionCurrentUrl = editCurrentQuestionImageImg?.getAttribute('src') || '';
                    const answerCurrentUrl = editCurrentAnswerImageImg?.getAttribute('src') || '';

                    setCurrentImage(
                        editCurrentQuestionImage,
                        editCurrentQuestionImageImg,
                        answerCurrentUrl
                    );

                    setCurrentImage(
                        editCurrentAnswerImage,
                        editCurrentAnswerImageImg,
                        questionCurrentUrl
                    );

                    const questionRemoveValue = editRemoveQuestionImage.value;
                    editRemoveQuestionImage.value = editRemoveAnswerImage.value;
                    editRemoveAnswerImage.value = questionRemoveValue;

                    const questionRemoveChecked = editRemoveQuestionImage.checked;
                    editRemoveQuestionImage.checked = editRemoveAnswerImage.checked;
                    editRemoveAnswerImage.checked = questionRemoveChecked;
                });
            }

            const continueCreateInput = document.getElementById('continueCreateInput');
            const continueCreateButton = document.getElementById('continueCreateButton');
            const createSubmitButton = document.getElementById('createSubmitButton');

            if (continueCreateButton && continueCreateInput) {
                continueCreateButton.addEventListener('click', function () {
                    continueCreateInput.value = '1';
                });
            }

            if (createSubmitButton && continueCreateInput) {
                createSubmitButton.addEventListener('click', function () {
                    continueCreateInput.value = '0';
                });
            }

            @if (session('continue_create'))
                window.setTimeout(function () {
                    openCardModal();

                    const createForm = document.getElementById('cardCreateForm');

                    if (createForm) {
                        createForm.reset();
                    }

                    resetPreview('createQuestionImagePreview');
                    resetPreview('createAnswerImagePreview');

                    const createCategory = document.getElementById('category_id');

                    if (createCategory) {
                        createCategory.value = @json($categoryId ?? '');
                    }

                    const questionField = document.getElementById('question');

                    if (questionField) {
                        questionField.focus();
                    }
                }, 50);
            @endif

        });
    </script>
</body>

</html>
