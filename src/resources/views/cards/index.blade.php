<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>カード管理 | StudyFlow</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}?v=20260907-selection-clean">

    <style>
        .message-fade-out {
            opacity: 0;
            transform: translateY(-4px);
            transition:
                opacity 0.35s ease,
                transform 0.35s ease;
        }

        .form-submit-disabled {
            opacity: 0.65;
            cursor: wait !important;
        }
    </style>
</head>

<body>
    <header class="card-topbar">
        <div class="card-topbar-brand">
            <a href="{{ route('dashboard.index') }}" class="card-topbar-logo">
                StudyFlow
            </a>

            <span class="card-topbar-divider"></span>

            <span class="card-topbar-page">
                カード管理
            </span>
        </div>

        <a href="{{ route('dashboard.index') }}" class="dashboard-back-btn">
            ← ダッシュボードへ戻る
        </a>
    </header>

    <main class="content">
        <h1>カード管理</h1>

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

        <section class="category-section" id="categorySection">
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

        <div class="card-list-header">
            <form action="{{ route('cards.index') }}" method="GET" class="search-box">
                @if (!empty($categoryId))
                    <input type="hidden" name="category_id" value="{{ $categoryId }}">
                @endif

                <input type="text"
                    name="keyword"
                    value="{{ $keyword ?? '' }}"
                    placeholder="キーワード検索">

                <button type="submit">
                    検索
                </button>

                <a href="{{ route('cards.index', !empty($categoryId) ? ['category_id' => $categoryId] : []) }}">
                    リセット
                </a>
            </form>

            <button type="button" class="small-create-card-btn" onclick="openCardModal()">
                ＋ 新規カード
            </button>
        </div>

        <div class="selection-toolbar" id="selectionToolbar">
            <div class="selection-start" id="selectionStart">
                <button type="button" class="selection-mode-btn" id="selectionModeButton">
                    <span class="selection-mode-icon">☑</span>
                    カードを選択
                </button>
            </div>

            <div class="selection-active-bar" id="selectionActiveBar" hidden>
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
                        <th>レベル</th>
                        <th>学習回数</th>
                        <th>次回復習日</th>
                        <th></th>
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
                                : today();

                            $daysLeft = (int) now()
                                ->startOfDay()
                                ->diffInDays($reviewDate, false);

                            $cardCategoryId = $card->categories->first()?->id ?? '';
                        @endphp

                        <tr class="card-row">
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

                            <td>{{ $card->question }}</td>
                            <td>{{ $card->answer }}</td>

                            <td>
                                <span class="level-badge level-{{ $currentLevel }}">
                                    <span class="level-number">
                                        Lv.{{ $currentLevel }}
                                    </span>

                                    <span class="level-name">
                                        {{ $levelNames[$currentLevel] }}
                                    </span>
                                </span>
                            </td>

                            <td>{{ $card->study_count }} 回</td>

                            <td>
                                @if ($daysLeft < 0)
                                    <span class="review-badge review-overdue">
                                        期限切れ
                                    </span>
                                @elseif ($daysLeft === 0)
                                    <span class="review-badge review-today">
                                        今日
                                    </span>
                                @elseif ($daysLeft <= 3)
                                    <span class="review-badge review-soon">
                                        {{ $daysLeft }}日後
                                    </span>
                                @else
                                    <span class="review-badge review-later">
                                        {{ $daysLeft }}日後
                                    </span>
                                @endif

                                <div class="review-date">
                                    {{ $card->next_review_date ? $card->next_review_date->format('Y-m-d') : '-' }}
                                </div>
                            </td>

                            <td>
                                <div class="option-wrapper">
                                    <button type="button"
                                        class="option-btn"
                                        aria-label="カードの操作メニュー"
                                        aria-expanded="false">
                                        ︙
                                    </button>

                                    <div class="option-menu">
                                        <button type="button"
                                            class="edit-card-btn"
                                            data-question="{{ $card->question }}"
                                            data-answer="{{ $card->answer }}"
                                            data-category-id="{{ $cardCategoryId }}"
                                            data-update-url="{{ route('cards.update', $card->id) }}">
                                            編集
                                        </button>

                                        <form action="{{ route('cards.destroy', $card->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <input type="hidden" name="return_category_id" value="{{ $categoryId ?? '' }}">
                                            <input type="hidden" name="return_keyword" value="{{ $keyword ?? '' }}">

                                            <button type="submit"
                                                class="delete-option"
                                                onclick="return confirm('このカードを削除しますか？')">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-table-message">
                                カードがありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    {{-- 新規カード作成モーダル --}}
    <div id="cardCreateModal" class="modal-bg">
        <div class="modal-box">
            <h2>新規カード作成</h2>

            <form action="{{ route('cards.store') }}" method="POST">
                @csrf

                <input type="hidden" name="return_category_id" value="{{ $categoryId ?? '' }}">
                <input type="hidden" name="return_keyword" value="{{ $keyword ?? '' }}">

                <div class="form-group">
                    <label for="question">問題</label>
                    <textarea id="question" name="question" required></textarea>
                </div>

                <div class="form-group">
                    <label for="answer">解答</label>
                    <textarea id="answer" name="answer" required></textarea>
                </div>

                <div class="form-group">
                    <label for="category_id">カテゴリ</label>

                    <select id="category_id" name="category_id">
                        <option value="">カテゴリなし</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                @selected((string) $categoryId === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeCardModal()">
                        キャンセル
                    </button>

                    <button type="submit" class="btn-primary">
                        登録する
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- カード編集モーダル --}}
    <div id="cardEditModal" class="modal-bg">
        <div class="modal-box">
            <h2>カード編集</h2>

            <form id="cardEditForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="return_category_id" value="{{ $categoryId ?? '' }}">
                <input type="hidden" name="return_keyword" value="{{ $keyword ?? '' }}">

                <div class="form-group">
                    <label for="edit_question">問題</label>
                    <textarea id="edit_question" name="question" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_answer">解答</label>
                    <textarea id="edit_answer" name="answer" required></textarea>
                </div>

                <div class="form-group">
                    <label for="edit_category_id">カテゴリ</label>

                    <select id="edit_category_id" name="category_id">
                        <option value="">カテゴリなし</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeEditCardModal()">
                        キャンセル
                    </button>

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

            const editCardForm = document.getElementById('cardEditForm');
            const editQuestion = document.getElementById('edit_question');
            const editAnswer = document.getElementById('edit_answer');
            const editCategory = document.getElementById('edit_category_id');

            const categoryEditForm = document.getElementById('categoryEditForm');
            const editCategoryName = document.getElementById('edit_category_name');


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

            window.openEditCardModal = function (
                question,
                answer,
                categoryId,
                updateUrl
            ) {
                editQuestion.value = question ?? '';
                editAnswer.value = answer ?? '';
                editCategory.value = categoryId ?? '';
                editCardForm.action = updateUrl;
                editCardModal.classList.add('show');
                focusFirstField(editCardModal, '#edit_question');
            };

            window.closeEditCardModal = function () {
                editCardModal.classList.remove('show');
            };

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
                .querySelectorAll('.edit-card-btn')
                .forEach(function (button) {
                    button.addEventListener('click', function (event) {
                        event.stopPropagation();

                        openEditCardModal(
                            button.dataset.question,
                            button.dataset.answer,
                            button.dataset.categoryId,
                            button.dataset.updateUrl
                        );

                        closeAllMenus();
                    });
                });

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

            function setCategoryPanel(open, saveState = true) {
                categoryListPanel.hidden = !open;

                categoryToggleButton.setAttribute(
                    'aria-expanded',
                    open ? 'true' : 'false'
                );

                categoryChevronButton.setAttribute(
                    'aria-expanded',
                    open ? 'true' : 'false'
                );

                categoryChevronButton.textContent = open ? '▲' : '▼';

                categoryChevronButton.setAttribute(
                    'aria-label',
                    open ? 'カテゴリ一覧を閉じる' : 'カテゴリ一覧を開く'
                );

                if (saveState) {
                    localStorage.setItem(
                        'studyflowCategoryPanelOpen',
                        open ? '1' : '0'
                    );
                }
            }

            function toggleCategoryPanel() {
                setCategoryPanel(categoryListPanel.hidden);
            }

            /*
             * カテゴリ一覧の開閉状態を保持
             * ・前回開いていた場合は、ページ再読み込み後も開いたまま
             * ・カテゴリ絞り込み中は必ず開く
             */
            const hasSelectedCategory = @json(!empty($categoryId));
            const savedCategoryPanelState =
                localStorage.getItem('studyflowCategoryPanelOpen');

            if (hasSelectedCategory) {
                setCategoryPanel(true, false);
            } else if (savedCategoryPanelState === '1') {
                setCategoryPanel(true, false);
            } else {
                setCategoryPanel(false, false);
            }

            categoryToggleButton.addEventListener(
                'click',
                toggleCategoryPanel
            );

            categoryChevronButton.addEventListener(
                'click',
                toggleCategoryPanel
            );

            document.addEventListener('click', closeAllMenus);

            [
                cardModal,
                editCardModal,
                categoryModal,
                editCategoryModal,
                bulkCategoryModal
            ].forEach(function (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.classList.remove('show');
                    }
                });
            });

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

                selectionStart.hidden = false;
                selectionActiveBar.hidden = true;

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
                        if (!selectionMode) {
                            return;
                        }

                        if (
                            event.target.closest(
                                'input, button, a, form, .option-wrapper'
                            )
                        ) {
                            return;
                        }

                        checkbox.checked = !checkbox.checked;
                        updateSelectionState();
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
        });
    </script>
</body>

</html>
