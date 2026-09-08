<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学習 | StudyFlow</title>

    <link rel="stylesheet" href="{{ asset('css/study.css') }}">
    <link rel="stylesheet" href="{{ asset('css/study-category.css') }}">
    <link rel="stylesheet" href="{{ asset('css/study-answer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/study-progress.css') }}">
    <link rel="stylesheet" href="{{ asset('css/study-waiting.css') }}">
    <link rel="stylesheet" href="{{ asset('css/study-complete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/study-theme.css') }}?v=20260908-1">
</head>

<body class="study-page">

    <div class="study-background" aria-hidden="true">
        <span class="study-decor-circle study-decor-left-top"></span>
        <span class="study-decor-circle study-decor-left-bottom"></span>
        <span class="study-decor-circle study-decor-right-top"></span>
        <span class="study-decor-circle study-decor-right-bottom"></span>
        <span class="study-decor-line study-decor-line-left"></span>
        <span class="study-decor-line study-decor-line-right"></span>
    </div>

    <header class="study-topbar app-header">

        <a href="{{ route('dashboard.index') }}" class="app-brand" aria-label="StudyFlow ダッシュボードへ">

            <span class="app-brand-icon" aria-hidden="true">
                <svg viewBox="0 0 86 64">
                    <defs>
                        <linearGradient id="studyBookLeft" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#2196f3" />
                            <stop offset="100%" stop-color="#2677ea" />
                        </linearGradient>

                        <linearGradient id="studyBookRight" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#41c6cc" />
                            <stop offset="100%" stop-color="#35c783" />
                        </linearGradient>
                    </defs>

                    <path d="M7 8C17 5 27 6 36 11C40 13 42 16 43 19V57C38 52 32 49 25 47C19 45 13 45 7 47V8Z"
                        fill="url(#studyBookLeft)" />

                    <path d="M79 8C69 5 59 6 50 11C46 13 44 16 43 19V57C48 52 54 49 61 47C67 45 73 45 79 47V8Z"
                        fill="url(#studyBookRight)" />

                    <path d="M43 18V57" stroke="#ffffff" stroke-width="3" stroke-linecap="round" opacity="0.9" />
                </svg>
            </span>

            <span class="app-brand-text">
                <strong>StudyFlow</strong>
                <small>記憶を、少しずつ確かなものに。</small>
            </span>
        </a>

        <nav class="app-nav" aria-label="メインナビゲーション">

            <a href="{{ route('dashboard.index') }}" class="app-nav-link">
                <span class="app-nav-icon" aria-hidden="true">⌂</span>
                <span>ダッシュボード</span>
            </a>

            <a href="{{ route('cards.index') }}" class="app-nav-link">
                <span class="app-nav-icon" aria-hidden="true">▣</span>
                <span>カード管理</span>
            </a>

            <a href="{{ route('study.index') }}" class="app-nav-link is-active" aria-current="page">
                <span class="app-nav-icon" aria-hidden="true">▷</span>
                <span>学習開始</span>
            </a>

        </nav>

        <form action="{{ route('logout') }}" method="POST" class="header-logout-form">
            @csrf

            <button type="submit" class="header-logout-btn">
                <span aria-hidden="true">↪</span>
                ログアウト
            </button>
        </form>

    </header>

    <aside class="study-side-copy study-side-copy-left" aria-hidden="true">
        SMALL STEPS<br>
        BIG CHANGES
    </aside>

    <aside class="study-side-copy study-side-copy-right-top" aria-hidden="true">
        A Better You<br>
        One Card at a Time.
        <span></span>
    </aside>

    <aside class="study-side-copy study-side-copy-right-bottom" aria-hidden="true">
        学ぶことが、<br>
        きっと楽しくなる。
        <span></span>
    </aside>

    <main class="content">

        <div class="page-heading">
            <div>
                <h1>学習</h1>
                <p>今日のカードを少しずつ進めましょう。</p>
            </div>
        </div>

        <section class="study-counts" aria-label="学習状況">
            <div class="count-box learning">
                <span>学習中</span>
                <strong>{{ $displayLearningCount }}</strong>

                @if ($waitingLearningCount > 0)
                    <small class="count-note">待機中のカードを含む</small>
                @endif
            </div>

            <div class="count-box new">
                <span>新規</span>
                <strong>{{ $newCount }}</strong>
            </div>

            <div class="count-box review">
                <span>復習</span>
                <strong>{{ $reviewCount }}</strong>
            </div>
        </section>

        @if ($card || $waitingLearningCount > 0)
            <section class="study-progress-area">
                <span class="study-progress-label">
                    今日の学習
                    @if ($selectedCategory)
                        <small>（{{ $selectedCategory->name }}）</small>
                    @endif
                </span>

                <strong class="study-progress-count">
                    残り {{ $total }} 枚
                </strong>
            </section>
        @endif

        <section class="category-filter-area">
            <div class="category-filter">
                <button type="button" class="category-filter-button {{ $selectedCategory ? 'selected' : '' }}"
                    id="categoryFilterButton" aria-haspopup="true" aria-expanded="false">

                    <span class="category-filter-icon">▦</span>

                    <span class="category-filter-text">
                        {{ $selectedCategory?->name ?? 'すべてのカテゴリ' }}
                    </span>

                    <span class="category-filter-arrow">⌄</span>
                </button>

                <div class="category-filter-menu" id="categoryFilterMenu">
                    <a href="{{ route('study.index') }}"
                        class="category-filter-item {{ !$categoryId ? 'active' : '' }}">

                        <span class="category-filter-main">
                            <span class="category-check">
                                {{ !$categoryId ? '✓' : '' }}
                            </span>

                            <span>すべてのカテゴリ</span>
                        </span>

                        <strong>{{ $allRemainingCount }}</strong>
                    </a>

                    @foreach ($categories as $category)
                        @php
                            $remainingCount = $categoryRemainingCounts[$category->id] ?? 0;
                            $isSelected = (int) $categoryId === (int) $category->id;
                        @endphp

                        <a href="{{ route('study.index', ['category_id' => $category->id]) }}" class="category-filter-item
                                    {{ $isSelected ? 'active' : '' }}
                                    {{ $remainingCount === 0 ? 'is-empty' : '' }}">

                            <span class="category-filter-main">
                                <span class="category-check">
                                    {{ $isSelected ? '✓' : '' }}
                                </span>

                                <span>{{ $category->name }}</span>
                            </span>

                            <strong>{{ $remainingCount }}</strong>
                        </a>
                    @endforeach

                    @if ($categories->isEmpty())
                        <div class="category-filter-empty">
                            カテゴリがまだありません
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if ($card)
                @php
                    $statusNames = [
                        'new' => '新規',
                        'learning' => '学習中',
                        'review' => '復習',
                    ];

                    $levelNames = [
                        1 => '初心者',
                        2 => '学習中',
                        3 => '定着中',
                        4 => '習得',
                        5 => 'マスター',
                    ];

                    $currentLevel = max(1, min(5, (int) $card->level));
                    $currentStatus = $card->status ?? 'new';
                @endphp

                <section class="study-card">
                    <div class="study-card-header">
                        <div class="study-card-info">
                            <span class="status-badge status-{{ $currentStatus }}">
                                {{ $statusNames[$currentStatus] ?? '新規' }}
                            </span>

                            <span class="level-badge level-{{ $currentLevel }}">
                                Lv.{{ $currentLevel }}
                                {{ $levelNames[$currentLevel] }}
                            </span>
                        </div>

                        <div class="study-card-header-right">
                            <div class="today-answer-count">
                                <span>今日の回答</span>
                                <strong>{{ $todaySummary['total'] }}回</strong>
                            </div>

                            <div class="study-card-options">
                                <button type="button" class="option-btn" aria-label="カードの操作メニュー" aria-expanded="false">
                                    ︙
                                </button>

                                <div class="option-menu">
                                    <a href="{{ route('cards.edit', [
                                        'card' => $card->id,
                                        'from' => 'study',
                                        'category_id' => $categoryId,
                                    ]) }}">
                                        編集
                                    </a>

                                    <form action="{{ route('cards.destroy', $card->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="delete-option" onclick="return confirm('このカードを削除しますか？')">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="question-area">
                        <span class="question-label">問題</span>
                        <h2>{{ $card->question }}</h2>
                    </div>

                    <details class="answer-box">
                        <summary>答えを見る</summary>

                        <div class="answer-content">
                            {{ $card->answer }}
                        </div>
                    </details>

                    <div class="study-actions" id="studyActions">
                        @foreach ([
                                ['value' => 'again', 'class' => 'again-btn', 'label' => 'もう一度'],
                                ['value' => 'hard', 'class' => 'hard-btn', 'label' => '難しい'],
                                ['value' => 'good', 'class' => 'good-btn', 'label' => '良い'],
                                ['value' => 'easy', 'class' => 'easy-btn', 'label' => '簡単'],
                            ] as $action)
                            <form action="{{ route('study.result', $card->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="result" value="{{ $action['value'] }}">

                                @if ($categoryId)
                                    <input type="hidden" name="category_id" value="{{ $categoryId }}">
                                @endif

                                <button type="submit" class="{{ $action['class'] }}">
                                    <span class="answer-result">{{ $action['label'] }}</span>
                                    <span class="answer-interval">{{ $answerIntervals[$action['value']] }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </section>

        @elseif ($waitingLearningCount > 0)

            <section class="study-card state-card study-waiting">
                <div class="state-icon">⏳</div>

                <h2>学習中カードの待機時間です</h2>

                <p>
                    現在出題できるカードはありません。<br>
                    <strong>{{ $waitingLearningCount }}枚</strong> のカードが、
                    しばらくすると再出題されます。
                </p>

                <p class="state-subtext">
                    少し休憩してからページを更新してください。
                </p>

                @if ($nextLearningCard)
                    <div class="countdown-area">
                        <p>次のカードまで</p>

                        <div id="countdown" data-review-at="{{ $nextLearningCard->review_at->timestamp * 1000 }}">
                            --
                        </div>

                        <div class="next-review-time">
                            再出題予定
                            <strong>
                                {{ $nextLearningCard->review_at->timezone('Asia/Tokyo')->format('H:i') }}
                            </strong>
                        </div>
                    </div>
                @endif

                <div class="waiting-summary">
                    <div class="waiting-summary-item">
                        <span class="waiting-summary-icon">◷</span>
                        <span>待機中のカード：</span>
                        <strong>{{ $waitingLearningCount }}枚</strong>
                    </div>

                    <div class="waiting-summary-item">
                        <span>今日の回答</span>
                        <strong>{{ $todaySummary['total'] }}回</strong>
                    </div>
                </div>

                <a href="{{ route('study.index', $categoryId ? ['category_id' => $categoryId] : []) }}"
                    class="study-btn waiting-refresh-btn">
                    <span aria-hidden="true">↻</span>
                    更新する
                </a>
            </section>

        @else

            @php
                $otherStudyCategories = $categories->filter(function ($category) use ($categoryRemainingCounts, $categoryId) {
                    $remaining = $categoryRemainingCounts[$category->id] ?? 0;

                    return $remaining > 0
                        && (int) $category->id !== (int) $categoryId;
                });
            @endphp

            <section class="study-card state-card study-complete">
                <div class="complete-icon">✓</div>

                @if ($selectedCategory)
                    <p class="complete-eyebrow">CATEGORY COMPLETE</p>
                    <h2>{{ $selectedCategory->name }}の今日の学習が完了しました！</h2>
                    <p class="complete-message">
                        このカテゴリで今日取り組むカードはすべて完了です。
                    </p>
                @else
                    <p class="complete-eyebrow">TODAY COMPLETE</p>
                    <h2>今日の学習が完了しました！</h2>
                    <p class="complete-message">
                        今日取り組むカードはすべて完了です。お疲れさまでした。
                    </p>
                @endif

                <div class="complete-summary">
                    <div class="complete-summary-primary">
                        <span>今日の回答</span>
                        <strong>{{ $todaySummary['total'] }}</strong>
                        <small>回</small>
                    </div>

                    <div class="complete-result-grid">
                        <div class="complete-result-item again">
                            <span>もう一度</span>
                            <strong>{{ $todaySummary['again'] }}</strong>
                        </div>

                        <div class="complete-result-item hard">
                            <span>難しい</span>
                            <strong>{{ $todaySummary['hard'] }}</strong>
                        </div>

                        <div class="complete-result-item good">
                            <span>良い</span>
                            <strong>{{ $todaySummary['good'] }}</strong>
                        </div>

                        <div class="complete-result-item easy">
                            <span>簡単</span>
                            <strong>{{ $todaySummary['easy'] }}</strong>
                        </div>
                    </div>

                    <div class="complete-rate">
                        <div>
                            <span>定着率</span>
                            <small>「良い」「簡単」の割合</small>
                        </div>

                        <strong>{{ $todaySummary['correct_rate'] }}%</strong>
                    </div>
                </div>

                @if ($selectedCategory && $otherStudyCategories->isNotEmpty())
                    <div class="next-category-area">
                        <div class="next-category-heading">
                            <div>
                                <span>まだ学習できるカテゴリがあります</span>
                                <strong>続けて学習しますか？</strong>
                            </div>

                            <small>残り {{ $allRemainingCount }} 枚</small>
                        </div>

                        <div class="next-category-list">
                            @foreach ($otherStudyCategories->take(4) as $category)
                                <a href="{{ route('study.index', ['category_id' => $category->id]) }}" class="next-category-item">
                                    <span>{{ $category->name }}</span>
                                    <strong>
                                        {{ $categoryRemainingCounts[$category->id] ?? 0 }}枚
                                    </strong>
                                    <span class="next-category-arrow">→</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="complete-actions">
                    @if ($selectedCategory && $allRemainingCount > 0)
                        <a href="{{ route('study.index') }}" class="complete-primary-btn">
                            すべてのカテゴリを学習
                        </a>
                    @endif

                    <a href="{{ route('dashboard.index') }}" class="complete-secondary-btn">
                        ダッシュボードへ戻る
                    </a>
                </div>
            </section>
        @endif
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const answerBox = document.querySelector('.answer-box');
            const studyActions = document.getElementById('studyActions');

            if (answerBox && studyActions) {
                studyActions.hidden = true;

                answerBox.addEventListener('toggle', function () {
                    studyActions.hidden = !answerBox.open;
                });
            }

            const optionButton = document.querySelector('.option-btn');
            const optionMenu = document.querySelector('.option-menu');

            if (optionButton && optionMenu) {
                optionButton.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const shouldOpen = !optionMenu.classList.contains('show');

                    optionMenu.classList.toggle('show', shouldOpen);
                    optionButton.setAttribute(
                        'aria-expanded',
                        shouldOpen ? 'true' : 'false'
                    );
                });

                optionMenu.addEventListener('click', function (event) {
                    event.stopPropagation();
                });

                document.addEventListener('click', function () {
                    optionMenu.classList.remove('show');
                    optionButton.setAttribute('aria-expanded', 'false');
                });
            }

            const categoryFilterButton = document.getElementById('categoryFilterButton');
            const categoryFilterMenu = document.getElementById('categoryFilterMenu');

            if (categoryFilterButton && categoryFilterMenu) {
                categoryFilterButton.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const shouldOpen = !categoryFilterMenu.classList.contains('show');

                    categoryFilterMenu.classList.toggle('show', shouldOpen);
                    categoryFilterButton.classList.toggle('open', shouldOpen);
                    categoryFilterButton.setAttribute(
                        'aria-expanded',
                        shouldOpen ? 'true' : 'false'
                    );
                });

                categoryFilterMenu.addEventListener('click', function (event) {
                    event.stopPropagation();
                });

                document.addEventListener('click', function () {
                    categoryFilterMenu.classList.remove('show');
                    categoryFilterButton.classList.remove('open');
                    categoryFilterButton.setAttribute('aria-expanded', 'false');
                });
            }

            const countdown = document.getElementById('countdown');

            if (countdown) {
                const reviewAt = Number(countdown.dataset.reviewAt);

                function updateCountdown() {
                    const diff = reviewAt - Date.now();

                    if (diff <= 0) {
                        countdown.textContent = '00:00';
                        location.reload();
                        return false;
                    }

                    const totalSeconds = Math.ceil(diff / 1000);
                    const minutes = Math.floor(totalSeconds / 60);
                    const seconds = totalSeconds % 60;

                    countdown.textContent =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(seconds).padStart(2, '0');

                    return true;
                }

                if (updateCountdown()) {
                    const timer = setInterval(function () {
                        if (!updateCountdown()) {
                            clearInterval(timer);
                        }
                    }, 1000);
                }
            }
        });
    </script>
</body>

</html>