<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード</title>

    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v=20260908-3">
</head>

<body class="dashboard-page">

    <div class="dashboard-background" aria-hidden="true">
        <span class="decor-circle decor-circle-left-top"></span>
        <span class="decor-circle decor-circle-left-bottom"></span>
        <span class="decor-circle decor-circle-right-top"></span>
        <span class="decor-circle decor-circle-right-bottom"></span>
        <span class="decor-line decor-line-left"></span>
        <span class="decor-line decor-line-right"></span>
    </div>

    <header class="topbar">

        <div class="topbar-brand">

            <div class="brand-row">
                <div class="book-logo" aria-hidden="true">
                    <svg viewBox="0 0 86 64">
                        <defs>
                            <linearGradient id="headerBookLeft" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#2196f3" />
                                <stop offset="100%" stop-color="#2677ea" />
                            </linearGradient>

                            <linearGradient id="headerBookRight" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#41c6cc" />
                                <stop offset="100%" stop-color="#35c783" />
                            </linearGradient>
                        </defs>

                        <path
                            d="M7 8C17 5 27 6 36 11C40 13 42 16 43 19V57C38 52 32 49 25 47C19 45 13 45 7 47V8Z"
                            fill="url(#headerBookLeft)" />

                        <path
                            d="M79 8C69 5 59 6 50 11C46 13 44 16 43 19V57C48 52 54 49 61 47C67 45 73 45 79 47V8Z"
                            fill="url(#headerBookRight)" />

                        <path d="M43 18V57" stroke="#ffffff" stroke-width="3" stroke-linecap="round" opacity="0.9" />
                    </svg>
                </div>

                <div>
                    <div class="topbar-logo">StudyFlow</div>
                    <p class="topbar-tagline">
                        記憶を、少しずつ確かなものに。
                    </p>
                </div>
            </div>

        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="logout-btn">
                <span class="logout-icon" aria-hidden="true">↪</span>
                ログアウト
            </button>
        </form>

    </header>

    <aside class="dashboard-side-copy dashboard-side-copy-left">
        <p>SMALL STEPS<br>BIG CHANGES</p>
    </aside>

    <aside class="dashboard-side-copy dashboard-side-copy-right-top">
        <p>A Better You<br>One Card at a Time.</p>
        <span></span>
    </aside>

    <aside class="dashboard-side-copy dashboard-side-copy-right-bottom">
        <p>学ぶことが、<br>きっと楽しくなる。</p>
        <span></span>
    </aside>

    <div class="layout">

        <main class="content">

            <section class="dashboard-heading">
                <h1>ダッシュボード</h1>

                <p class="dashboard-message">
                    今日もコツコツ続けて、記憶を定着させましょう！
                </p>
            </section>

            <div class="dashboard-cards">

                <div class="dashboard-quick-actions">

                    <a href="{{ route('study.index') }}" class="quick-action quick-action-study">

                        <span class="quick-action-icon quick-action-icon-play">
                            ▶
                        </span>

                        <span class="quick-action-text">
                            <strong>学習を始める</strong>
                            <small>
                                今すぐ学習できるカード：{{ $todayStudyCardCount }}枚
                            </small>
                        </span>

                        <span class="quick-action-arrow">→</span>

                    </a>

                    <a href="{{ route('cards.index') }}" class="quick-action quick-action-cards">

                        <span class="quick-action-icon">
                            ▣
                        </span>

                        <span class="quick-action-text">
                            <strong>カード管理</strong>
                            <small>カードの追加・編集・カテゴリ管理</small>
                        </span>

                        <span class="quick-action-arrow">→</span>

                    </a>

                </div>

                <section class="dashboard-section">

                    <h2 class="dashboard-section-title">
                        <span class="section-title-icon section-title-icon-blue">▥</span>
                        現在の学習状況
                    </h2>

                    <div class="dashboard-status-summary">

                        <div class="summary-card summary-new">

                            <div class="summary-top">
                                <span class="summary-icon">▤</span>

                                <div>
                                    <h3>新規</h3>

                                    <p class="dashboard-number">
                                        {{ $newCardCount }}
                                        <span>枚</span>
                                    </p>
                                </div>
                            </div>

                            <p class="summary-description">
                                まだ学習していないカード
                            </p>

                        </div>

                        <div class="summary-card summary-learning">

                            <div class="summary-top">
                                <span class="summary-icon">◆</span>

                                <div>
                                    <h3>学習中</h3>

                                    <p class="dashboard-number">
                                        {{ $learningCardCount }}
                                        <span>枚</span>
                                    </p>
                                </div>
                            </div>

                            <p class="summary-description">
                                今すぐ {{ $availableLearningCount }}枚
                                ・待機中 {{ $waitingLearningCount }}枚
                            </p>

                        </div>

                        <div class="summary-card summary-review">

                            <div class="summary-top">
                                <span class="summary-icon">⟳</span>

                                <div>
                                    <h3>復習</h3>

                                    <p class="dashboard-number">
                                        {{ $reviewCardCount }}
                                        <span>枚</span>
                                    </p>
                                </div>
                            </div>

                            <p class="summary-description">
                                復習期限が来ているカード
                            </p>

                        </div>

                    </div>

                </section>

                <section class="dashboard-section">

                    <h2 class="dashboard-section-title">
                        <span class="section-title-icon section-title-icon-blue">♜</span>
                        今日の実績
                    </h2>

                    <div class="dashboard-performance">

                        <div class="performance-card performance-study">

                            <div class="performance-top">
                                <span class="performance-icon">✓</span>

                                <div>
                                    <h3>今日の学習回数</h3>

                                    <p class="dashboard-number">
                                        {{ $todayStudyCount }}
                                        <span>回</span>
                                    </p>
                                </div>
                            </div>

                        </div>

                        <div class="performance-card performance-accuracy">

                            <div class="performance-top">
                                <span class="performance-icon">◎</span>

                                <div>
                                    <h3>正答率</h3>

                                    <p class="dashboard-number">
                                        {{ $accuracyRate }}
                                        <span>%</span>
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>

                </section>

            </div>

            <h2 class="dashboard-data-title">
                <span class="section-title-icon section-title-icon-blue">▥</span>
                学習データ
            </h2>

            <div class="dashboard-bottom-grid">

                <section class="dashboard-chart-card">

                    <h2>直近7日間の学習回数</h2>

                    <div class="chart-container">
                        <canvas id="studyChart"></canvas>
                    </div>

                </section>

                <section class="dashboard-status-card">

                    <h2>カードの状態</h2>

                    <p class="status-total-top">
                        全カード {{ $totalCardCount }}枚
                    </p>

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

                </section>

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
                    backgroundColor: function (context) {
                        const chart = context.chart;
                        const { ctx, chartArea } = chart;

                        if (!chartArea) {
                            return '#31a6ee';
                        }

                        const gradient = ctx.createLinearGradient(
                            0,
                            chartArea.top,
                            0,
                            chartArea.bottom
                        );

                        gradient.addColorStop(0, '#2797f2');
                        gradient.addColorStop(1, '#4acfc2');

                        return gradient;
                    },
                    borderRadius: 7,
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
                            precision: 0,
                            color: '#67819e'
                        },
                        grid: {
                            color: 'rgba(102, 139, 177, 0.12)'
                        },
                        border: {
                            display: false
                        }
                    },

                    x: {
                        ticks: {
                            color: '#67819e'
                        },
                        grid: {
                            display: false
                        },
                        border: {
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
                        '#2c9cf0',
                        '#f4b94e',
                        '#4ccf91',
                        '#d7e3ea'
                    ],

                    borderColor: '#ffffff',
                    borderWidth: 4,
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
