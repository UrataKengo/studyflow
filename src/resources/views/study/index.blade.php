<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>学習開始</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">
            <div class="logo">StudyFlow</div>

            <nav class="menu">
                <a href="{{ route('dashboard.index') }}">ダッシュボード</a>
                <a href="{{ route('cards.index') }}">カード一覧</a>
                <a href="{{ route('categories.index') }}">カテゴリ</a>
                <a class="active" href="{{ route('study.index') }}">学習開始</a>
                <a href="{{ route('study-logs.index') }}">学習履歴</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
            </form>
        </aside>

        <main class="content">

            <h1>学習開始</h1>

            <div class="study-counts">
                <div class="count-box learning">
                    <span>学習中</span>
                    <strong>{{ $learningCount }}</strong>
                </div>

                <div class="count-box new">
                    <span>新規</span>
                    <strong>{{ $newCount }}</strong>
                </div>

                <div class="count-box review">
                    <span>復習</span>
                    <strong>{{ $reviewCount }}</strong>
                </div>
            </div>

            <div class="category-filter-area">
                <h2>学習するカテゴリ</h2>

                <div class="category-tabs">
                    <a href="{{ route('study.index') }}" class="{{ empty($categoryId) ? 'active-category' : '' }}">
                        すべて
                    </a>

                    @foreach ($categories as $category)
                        <a href="{{ route('study.index', ['category_id' => $category->id]) }}"
                            class="{{ ($categoryId ?? '') == $category->id ? 'active-category' : '' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if($card)
                <p class="study-progress">
                    1 / {{ $total }} 問
                </p>
            @endif

            @if ($card)

                <div class="study-card">

                    <div class="study-categories">
                        @forelse ($card->categories as $category)
                            <span>{{ $category->name }}</span>
                        @empty
                            <span>カテゴリなし</span>
                        @endforelse
                    </div>

                    <h2>{{ $card->question }}</h2>

                    <div class="study-card-options">
                        <button type="button" class="option-btn">︙</button>

                        <div class="option-menu">
                            <a href="{{ route('cards.edit', $card->id) }}">編集</a>

                            <form action="{{ route('cards.destroy', $card->id) }}" method="POST">
                                @csrf

                                @method('DELETE')

                                <button type="submit" onclick="return confirm('このカードを削除しますか？')">
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>

                    <details class="answer-box">
                        <summary>答えを見る</summary>

                        <div class="answer-content">
                            {{ $card->answer }}
                        </div>
                    </details>

                    <div class="study-actions">

                        <form action="{{ route('study.result', $card->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="result" value="again">
                            <input type="hidden" name="category_id" value="{{ $categoryId }}">
                            <button class="again-btn">もう一度</button>
                        </form>

                        <form action="{{ route('study.result', $card->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="result" value="hard">
                            <input type="hidden" name="category_id" value="{{ $categoryId }}">
                            <button class="hard-btn">難しい</button>
                        </form>

                        <form action="{{ route('study.result', $card->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="result" value="good">
                            <input type="hidden" name="category_id" value="{{ $categoryId }}">
                            <button class="good-btn">良い</button>
                        </form>

                        <form action="{{ route('study.result', $card->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="result" value="easy">
                            <input type="hidden" name="category_id" value="{{ $categoryId }}">
                            <button class="easy-btn">簡単</button>
                        </form>

                    </div>

                </div>

            @elseif($waitingLearningCount > 0)

                <div class="study-card study-waiting">
                    <div class="complete-icon">⏳</div>

                    <h2>学習中カードの待機時間です</h2>

                    <p>
                        現在出題できるカードはありません。<br>
                        <strong>{{ $waitingLearningCount }}枚</strong> のカードが、
                        しばらくすると再出題されます。
                    </p>

                    <p>
                        少し休憩してからページを更新してください。
                    </p>

                    @if($nextLearningCard)
                        <div class="countdown-area">
                            <p>次のカードまで</p>

                            <div id="countdown" data-review-at="{{ $nextLearningCard->review_at->timestamp * 1000 }}">
                                --
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('study.index', ['category_id' => $categoryId]) }}" class="study-btn">
                        更新する
                    </a>
                </div>

            @else

                <div class="study-card study-complete">
                    <div class="complete-icon">🎉</div>

                    <h2>今日の学習お疲れさまでした！</h2>

                    <p>
                        本日の復習はすべて完了しました。
                    </p>

                    <a href="{{ route('dashboard.index') }}" class="study-btn">
                        ダッシュボードへ戻る
                    </a>
                </div>

            @endif

        </main>

        <script>
            const countdown = document.getElementById('countdown');

            if (countdown) {
                const reviewAt = Number(countdown.dataset.reviewAt);

                function updateCountdown() {
                    const now = Date.now();
                    const diff = reviewAt - now;

                    if (diff <= 0) {
                        countdown.textContent = '00:00';
                        location.reload();
                        return;
                    }

                    const totalSeconds = Math.ceil(diff / 1000);
                    const minutes = Math.floor(totalSeconds / 60);
                    const seconds = totalSeconds % 60;

                    countdown.textContent =
                        String(minutes).padStart(2, '0') +
                        ':' +
                        String(seconds).padStart(2, '0');
                }

                updateCountdown();

                const timer = setInterval(() => {
                    const diff = reviewAt - Date.now();

                    if (diff <= 0) {
                        clearInterval(timer);
                        countdown.textContent = '00:00';
                        location.reload();
                        return;
                    }

                    updateCountdown();
                }, 1000);
            }
        </script>
        </script>
    </div>
</body>

</html>