<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カード一覧</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">
            <div class="logo">StudyFlow</div>

            <nav class="menu">
                <a href="{{ route('dashboard.index') }}">ダッシュボード</a>
                <a class="active" href="{{ route('cards.index') }}">カード一覧</a>
                <a href="{{ route('study.index') }}">学習開始</a>
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

            <h1>カード管理</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="error-message">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('cards.index') }}" method="GET" class="search-box">

                <input type="text" name="keyword" value="{{ $keyword ?? '' }}" placeholder="キーワード検索">


                <button type="submit">
                    検索
                </button>

                <a href="{{ route('cards.index') }}">
                    リセット
                </a>

            </form>

            <div class="category-filter-area">
                <h2>カテゴリ</h2>

                <div class="category-tabs">
                    <a href="{{ route('cards.index') }}" class="{{ empty($categoryId) ? 'active-category' : '' }}">
                        すべて
                    </a>

                    @foreach ($categories as $category)
                        <a href="{{ route('cards.index', ['category_id' => $category->id]) }}"
                            class="{{ ($categoryId ?? '') == $category->id ? 'active-category' : '' }}">
                            {{ $category->name }} {{ $category->cards_count }}枚
                        </a>
                    @endforeach

                    <form action="{{ route('categories.store') }}" method="POST" class="category-add-mini">
                        @csrf
                        <input type="text" name="name" placeholder="カテゴリ名">
                        <button type="submit">＋</button>
                    </form>
                </div>
            </div>

            @if(!empty($categoryId))

                @php
                    $currentCategory = $categories->firstWhere('id', $categoryId);
                @endphp

                <div class="card-list-header">
                    <div class="category-title-with-menu">
                        <h2>
                            {{ $currentCategory?->name }}
                        </h2>

                        @if($currentCategory)
                            <div class="card-options">
                                <button type="button" class="option-btn">︙</button>

                                <div class="option-menu">
                                    <a href="{{ route('categories.edit', $currentCategory->id) }}">
                                        編集
                                    </a>

                                    <form action="{{ route('categories.destroy', $currentCategory->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" onclick="return confirm('このカテゴリを削除しますか？')">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-list-actions">
                        <a href="{{ route('cards.index') }}" class="back-category-btn">
                            ← カテゴリ選択に戻る
                        </a>

                        <button type="button" class="small-create-card-btn" onclick="openCardModal()">
                            ＋ 新規カード
                        </button>
                    </div>
                </div>

                <table class="card-table">
                    <thead>
                        <tr>
                            <th>問題</th>
                            <th>解答</th>
                            <th>レベル</th>
                            <th>学習回数</th>
                            <th>次回復習日</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $groupedCards = $cards->groupBy(function ($card) {
                                return $card->categories->first()->name ?? '未分類';
                            });
                        @endphp

                        @forelse ($groupedCards as $categoryName => $categoryCards)

                            <tr class="category-row">
                                <td colspan="6">
                                    {{ $categoryName }}
                                </td>
                            </tr>

                            @foreach ($categoryCards as $card)
                                <tr>
                                    <td>{{ $card->question }}</td>
                                    <td>{{ $card->answer }}</td>

                                    <td>
                                        @php
                                            $levelNames = [
                                                1 => '初心者',
                                                2 => '学習中',
                                                3 => '定着中',
                                                4 => '習得',
                                                5 => 'マスター',
                                            ];

                                            $currentLevel = max(1, min(5, (int) $card->level));
                                        @endphp

                                        <span class="level-badge level-{{ $currentLevel }}">
                                            <span class="level-number">
                                                Lv.{{ $currentLevel }}
                                            </span>

                                            <span class="level-name">
                                                {{ $levelNames[$currentLevel] }}
                                            </span>
                                        </span>
                                    </td>

                                    <td>{{ $card->review_count }} 回</td>

                                    <td>
                                        @php
                                            $reviewDate = \Carbon\Carbon::parse($card->next_review_date)->startOfDay();
                                            $daysLeft = now()->startOfDay()->diffInDays($reviewDate, false);
                                            $daysLeft = (int) $daysLeft;
                                        @endphp

                                        @if ($daysLeft < 0)
                                            <span class="review-badge review-overdue">期限切れ</span>
                                        @elseif ($daysLeft === 0)
                                            <span class="review-badge review-today">今日</span>
                                        @elseif ($daysLeft <= 3)
                                            <span class="review-badge review-soon">{{ $daysLeft }}日後</span>
                                        @else
                                            <span class="review-badge review-later">{{ $daysLeft }}日後</span>
                                        @endif

                                        <div class="review-date">
                                            {{ $card->next_review_date }}
                                        </div>
                                    </td>

                                    <td class="question-cell">
                                        <div class="card-options">
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
                                    </td>

                                </tr>
                            @endforeach

                        @empty
                            <tr>
                                <td colspan="6">カードがありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            @else

                <div class="empty-category">

                    <div class="empty-icon">
                        📚
                    </div>

                    <h2>カテゴリを選択してください</h2>

                    <p>
                        上のカテゴリをクリックすると<br>
                        カード一覧が表示されます。
                    </p>

                </div>

            @endif

            @if(!empty($categoryId))
                <div id="cardCreateModal" class="modal-bg">
                    <div class="modal-box">

                        <h2>新規カード作成</h2>

                        <form action="{{ route('cards.store') }}" method="POST">
                            @csrf

                            <input type="hidden" name="category_id" value="{{ $categoryId }}">

                            <div class="form-group">
                                <label>問題</label>
                                <textarea name="question" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>解答</label>
                                <textarea name="answer" required></textarea>
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
            @endif
        </main>

    </div>

    <script>
        function openCardModal() {
            document.getElementById('cardCreateModal').style.display = 'flex';
        }

        function closeCardModal() {
            document.getElementById('cardCreateModal').style.display = 'none';
        }
    </script>
</body>

</html>