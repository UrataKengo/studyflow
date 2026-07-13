<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/cards.css') }}">
</head>

<body>
    <div class="content" style="max-width: 420px; margin: 80px auto;">

        <h1>StudyFlow ログイン</h1>

        @if ($errors->any())
            <ul style="color:red;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <p>
                <strong>メールアドレス</strong><br>
                <input type="email" name="email" required>
            </p>

            <p>
                <strong>パスワード</strong><br>
                <input type="password" name="password" required>
            </p>

            <button class="btn btn-primary" type="submit">
                ログイン
            </button>
        </form>

    </div>
</body>

</html>