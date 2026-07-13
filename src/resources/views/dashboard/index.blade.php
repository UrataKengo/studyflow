<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="layout">

        <aside class="sidebar">

            <div class="logo">
                StudyFlow
            </div>

            <nav class="menu">
                <a class="active" href="{{ route('dashboard.index') }}">ダッシュボード</a>
                <a href="{{ route('cards.index') }}">カード一覧</a>
                <a href="{{ route('categories.index') }}">カテゴリ</a>
                <a href="{{ route('study.index') }}">学習開始</a>
                <a href="{{ route('study-logs.index') }}">学習履歴</a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" style="margin-top:20px;">
                @csrf

                <button type="submit" style="
            width:100%;
            padding:12px;
            border:none;
            border-radius:8px;
            background:#dc3545;
            color:white;
            cursor:pointer;
        ">
                    ログアウト
                </button>
            </form>

        </aside>

        <main class="content">

            <h1>ダッシュボード</h1>

            <div class="dashboard-cards">

                <div class="dashboard-card">
                    <h3>今日の復習</h3>

                    <div class="dashboard-number">
                        {{ $todayReviewCount }}
                        <span>枚</span>
                    </div>

                    @if ($todayReviewCount > 0)
                        <a href="{{ route('study.index') }}" class="study-start-btn">
                            学習開始
                        </a>
                    @endif

                </div>

                <div class="dashboard-card">
                    <h3>登録カード数</h3>
                    <p class="dashboard-number">{{ $cardCount }}<span>枚</span></p>
                    <p>全カードの合計</p>
                </div>

                <div class="dashboard-card">
                    <h3>学習記録</h3>
                    <p class="dashboard-number">{{ $studyDays }}<span>日</span></p>
                    <p>連続学習日数</p>
                </div>

                <div class="dashboard-card">
                    <h3>総学習回数</h3>
                    <p class="dashboard-number">{{ $studyCount }}<span>回</span></p>
                    <p>これまでの学習回数</p>
                </div>

                <div class="dashboard-card">
                    <h3>今日の学習</h3>
                    <p class="dashboard-number">{{ $todayStudyCount }}<span>回</span></p>
                    <p>今日学習した回数</p>
                </div>

                <div class="dashboard-card">
                    <h3>正答率</h3>
                    <p class="dashboard-number">{{ $accuracyRate }}<span>%</span></p>
                    <p>学習結果の正答率</p>
                </div>

            </div>

        </main>

    </div>
</body>

</html>