<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>

    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v=20260907-2">
</head>

<body>

    <header class="topbar">

        <div class="topbar-brand">

                <div class="topbar-logo">
                    StudyFlow
                </div>

                <p class="topbar-tagline">
                    記憶を、少しずつ確かなものに。
                </p>

        </div>

        <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="logout-btn">
                    ログアウト
                </button>
        </form>

    </header>

    <div class="layout">

            <main class="content">

                <h1>ダッシュボード</h1>

                <p class="dashboard-message">
                    今日もコツコツ続けて、記憶を定着させましょう！
                </p>

                <div class="dashboard-cards">

                    <div class="dashboard-quick-actions">

                        <a href="{{ route('study.index') }}" class="quick-action quick-action-study">
                            <span class="quick-action-icon">▶</span>
                            <span class="quick-action-text">
                                <strong>学習を始める</strong>
                                <small>今すぐ学習できるカード：{{ $todayStudyCardCount }}枚</small>
                            </span>
                            <span class="quick-action-arrow">→</span>
                        </a>

                        <a href="{{ route('cards.index') }}" class="quick-action quick-action-cards">
                            <span class="quick-action-icon">▣</span>
                            <span class="quick-action-text">
                                <strong>カード管理</strong>
                                <small>カードの追加・編集・カテゴリ管理</small>
                            </span>
                            <span class="quick-action-arrow">→</span>
                        </a>

                    </div>

                    <div class="dashboard-section">

                        <h2 class="dashboard-section-title">
                            現在の学習状況
                        </h2>

                        <div class="dashboard-status-summary">

                            <div class="summary-card summary-new">

                                <h3>新規</h3>

                                <p class="dashboard-number">
                                    {{ $newCardCount }}
                                    <span>枚</span>
                                </p>

                                <p>
                                    まだ学習していないカード
                                </p>

                            </div>


                            <div class="summary-card summary-learning">

                                <h3>学習中</h3>

                                <p class="dashboard-number">
                                    {{ $learningCardCount }}
                                    <span>枚</span>
                                </p>

                                <p>
                                    今すぐ {{ $availableLearningCount }}枚
                                    ・待機中 {{ $waitingLearningCount }}枚
                                </p>

                            </div>


                            <div class="summary-card summary-review">

                                <h3>復習</h3>

                                <p class="dashboard-number">
                                    {{ $reviewCardCount }}
                                    <span>枚</span>
                                </p>

                                <p>
                                    復習期限が来ているカード
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="dashboard-section">

                        <h2 class="dashboard-section-title">
                            今日の実績
                        </h2>

                        <div class="dashboard-performance">

                            <div class="performance-card">

                                <h3>今日の学習回数</h3>

                                <p class="dashboard-number">
                                    {{ $todayStudyCount }}
                                    <span>回</span>
                                </p>

                            </div>


                            <div class="performance-card">

                                <h3>正答率</h3>

                                <p class="dashboard-number">
                                    {{ $accuracyRate }}
                                    <span>%</span>
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                <h2 class="dashboard-data-title">
                    学習データ
                </h2>

                <div class="dashboard-bottom-grid">

                    <div class="dashboard-chart-card">

                        <h2>直近7日間の学習回数</h2>

                        <div class="chart-container">
                            <canvas id="studyChart"></canvas>
                        </div>

                    </div>

                    <div class="dashboard-status-card">

                        <h2>カードの状態</h2>

                        <p>全カード {{ $totalCardCount }}枚</p>

                        <div class="status-chart-area">

                            <div class="status-chart-container">
                                <canvas id="statusChart"></canvas>
                            </div>

                            <div class="status-list">

                                <div class="status-item">
                                    <span class="status-dot status-new"></span>
                                    <span>新規</span>
                                    <strong>{{ $newCardCount }}枚</strong>
                                </div>

                                <div class="status-item">
                                    <span class="status-dot status-learning"></span>
                                    <span>学習中</span>
                                    <strong>{{ $learningCardCount }}枚</strong>
                                </div>

                                <div class="status-item">
                                    <span class="status-dot status-review"></span>
                                    <span>復習期限済み</span>
                                    <strong>{{ $reviewCardCount }}枚</strong>
                                </div>

                                <div class="status-item">
                                    <span class="status-dot status-other"></span>
                                    <span>復習待ち</span>
                                    <strong>{{ $otherCardCount }}枚</strong>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const studyChartLabels = @json($studyChartLabels);
            const studyChartData = @json($studyChartData);

            const studyChart = document.getElementById('studyChart');

            new Chart(studyChart, {
                type: 'bar',

                data: {
                    labels: studyChartLabels,

                    datasets: [{
                        label: '学習回数',
                        data: studyChartData,
                        backgroundColor: '#72c987',
                        borderRadius: 6,
                        barThickness: 38
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#edf0ee'
                            }
                        },

                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
    </script>

    <script>
        const statusChart = document.getElementById('statusChart');

            new Chart(statusChart, {
                type: 'doughnut',

                data: {
                    labels: [
                        '新規',
                        '学習中',
                        '復習期限済み',
                        '復習待ち'
                    ],

                    datasets: [{
                        data: [
                            {{ $newCardCount }},
                            {{ $learningCardCount }},
                            {{ $reviewCardCount }},
                            {{ $otherCardCount }}
                        ],

                        backgroundColor: [
                            '#4f9cf9',
                            '#f3b33d',
                            '#8b6de0',
                            '#d9dedb'
                        ],

                        borderWidth: 0,
                        hoverOffset: 5
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.label + '：' + context.raw + '枚';
                                }
                            }
                        }
                    }
                }
            });
        </script>
</body>

</html>