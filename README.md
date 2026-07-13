# Laravel Sample

## 必要なもの

事前に以下をインストールしてください。

- Docker Desktop
- Git

---

## プロジェクト取得

リポジトリを clone します。

```bash
git clone リポジトリURL
```

プロジェクトへ移動します。

```bash
cd Laravel-sample
```

---

## Docker 起動

Docker コンテナを起動します。

```bash
docker compose up --build
```

---

## 動作確認

ブラウザで下記へアクセスしてください。  

```txt
http://localhost:8001
```

Laravel の初期画面が表示されれば成功です。

---

## 停止方法

コンテナ停止：

```bash
docker compose down
```