<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow ログイン</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v=20260908-1">
</head>

<body class="login-page">

    <div class="login-background" aria-hidden="true">
        <span class="decor-circle decor-circle-left-top"></span>
        <span class="decor-circle decor-circle-left"></span>
        <span class="decor-circle decor-circle-right-top"></span>
        <span class="decor-circle decor-circle-right-bottom"></span>

        <span class="decor-line decor-line-left"></span>
        <span class="decor-line decor-line-right"></span>
    </div>

    <header class="login-brand">
        <div class="login-brand-name">StudyFlow</div>
        <p>記憶を、少しずつ確かなものに。</p>
    </header>

    <aside class="login-message login-message-left">
        <p class="login-message-main">
            今日の<br>
            ひとつが、<br>
            明日の自分をつくる。
        </p>

        <span class="message-line"></span>

        <p class="login-message-en">
            SMALL STEPS<br>
            BIG CHANGES
        </p>
    </aside>

    <aside class="login-message login-message-right-top">
        <p>
            A Better You<br>
            One Card at a Time.
        </p>

        <span class="message-line"></span>
    </aside>

    <aside class="login-message login-message-right-bottom">
        <p>
            学ぶことが、<br>
            きっと楽しくなる。
        </p>

        <span class="message-line"></span>
    </aside>

    <main class="login-main">

        <section class="login-card">

            <div class="login-card-heading">

                <div class="book-logo" aria-hidden="true">
                    <svg viewBox="0 0 86 64" role="img">
                        <defs>
                            <linearGradient id="bookGradientLeft" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#2196f3" />
                                <stop offset="100%" stop-color="#2677ea" />
                            </linearGradient>

                            <linearGradient id="bookGradientRight" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#41c6cc" />
                                <stop offset="100%" stop-color="#35c783" />
                            </linearGradient>
                        </defs>

                        <path
                            d="M7 8C17 5 27 6 36 11C40 13 42 16 43 19V57C38 52 32 49 25 47C19 45 13 45 7 47V8Z"
                            fill="url(#bookGradientLeft)" />

                        <path
                            d="M79 8C69 5 59 6 50 11C46 13 44 16 43 19V57C48 52 54 49 61 47C67 45 73 45 79 47V8Z"
                            fill="url(#bookGradientRight)" />

                        <path d="M43 18V57" stroke="#ffffff" stroke-width="3" stroke-linecap="round" opacity="0.9" />
                    </svg>
                </div>

                <h1>
                    <span>Study</span><strong>Flow</strong>
                </h1>

                <p>
                    記憶を、少しずつ確かなものに。
                </p>

            </div>

            @if ($errors->any())
                <div class="login-error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="login-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="email">
                        メールアドレス
                    </label>

                    <div class="input-wrap">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M3.5 6.5h17v11h-17z" />
                                <path d="m4.5 7.5 7.5 6 7.5-6" />
                            </svg>
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="test@example.com"
                            autocomplete="email"
                            required
                            autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        パスワード
                    </label>

                    <div class="input-wrap">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <rect x="5.5" y="10" width="13" height="10" rx="2" />
                                <path d="M8.5 10V7.7A3.5 3.5 0 0 1 12 4.2a3.5 3.5 0 0 1 3.5 3.5V10" />
                            </svg>
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="パスワードを入力"
                            autocomplete="current-password"
                            required>

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="パスワードを表示"
                            aria-pressed="false">

                            <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                <circle cx="12" cy="12" r="2.8" />
                            </svg>

                            <svg class="eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3 3l18 18" />
                                <path d="M10.3 6.2A9.4 9.4 0 0 1 12 6c6 0 9.5 6 9.5 6a16.6 16.6 0 0 1-2.6 3.2" />
                                <path d="M6.2 6.2C3.8 8 2.5 12 2.5 12s3.5 6 9.5 6a9.7 9.7 0 0 0 3-.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="login-submit">
                    <span>ログイン</span>
                    <span class="login-arrow" aria-hidden="true">→</span>
                </button>

            </form>

            <div class="login-card-footer">
                <span></span>
                <p>学びを、もっと身近に</p>
                <span></span>
            </div>

        </section>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const password = document.getElementById('password');
            const toggle = document.getElementById('passwordToggle');

            if (!password || !toggle) {
                return;
            }

            toggle.addEventListener('click', function () {
                const isVisible = password.type === 'text';

                password.type = isVisible ? 'password' : 'text';
                toggle.classList.toggle('is-visible', !isVisible);
                toggle.setAttribute('aria-pressed', String(!isVisible));
                toggle.setAttribute(
                    'aria-label',
                    isVisible ? 'パスワードを表示' : 'パスワードを隠す'
                );
            });
        });
    </script>

</body>

</html>
