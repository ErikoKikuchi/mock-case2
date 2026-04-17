# mock-case2　勤怠管理  
  
## 目的  
模擬案件として勤怠管理アプリの作成を行う。  
簡易的な勤怠アプリで、勤務開始・勤務終了・休憩処理（複数可）の処理が可能。  
変更申請は複数まとめて実施可能。管理者が承認を行う。  

- 管理者が勤怠登録をする場合は一般ユーザーとして別のメールアドレスで登録してあることを前提とする。  
- 夜勤や休日管理などの要件はなし  
- 変更申請の内容については変更前との差異を表示しない設計なので、ユーザーが備考に詳細を記入する運用とする。  
  
## 使用技術(実行環境)  
| 技術 | バージョン |
|------|-----------|
| PHP | 8.4.18-fpm |
| Laravel | 12.53.0 |
| MySQL | 8.4.7 |
| nginx | 1.28.2 |
| Node.js | 22.22.0 |
| npm | 10.9.4 |
  
## 環境構築  
- Ubuntu使用にて構築  
  
### 1. リポジトリのクローン　［ホスト環境］
  
```bash
git clone git@github.com:ErikoKikuchi/mock-case2.git  
cd mock-case2  
git remote set-url origin 作成したリポジトリのURL  
```
  
### 2. Dockerの起動　［ホスト環境］
  
```bash
docker compose up -d --build  
```
  
  ### 3. Laravel初期設定　［PHPコンテナ内］
  
```bash
docker compose exec php bash   # コンテナに入る  
composer install  
cp .env.example .env  
php artisan key:generate  
php artisan migrate  
php artisan db:seed  
```
  
`.env` のDB接続情報は以下の通りです（デフォルト設定済み）：
DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass  
  
> **Windowsの場合**（権限エラーが出た場合）　［ホスト環境・rootディレクトリ］  
> ```bash  
> sudo chmod -R 777 src/*  
> ```  
### 4. Node.js / npm のインストール　［ホスト環境・rootディレクトリ］  
  
> Node.js / npm のインストールはホスト環境で行います。`package.json` は `src` ディレクトリにあります。  

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -  
sudo apt-get install -y nodejs  
```
  
### 5. フロントエンドのセットアップ　［srcディレクトリ］
  
```bash
cd src  
npm install  
npm run dev   # 開発環境用  
```
  
> `vite.config.js` で `build.outDir`（vite.config.jsからの相対パス）を確認し、`app.js` に読み込むファイルが設定されていることを確認してください。
  
---  
  
## メール設定（Mailtrap）
  
本アプリは開発環境のメール送信確認に [Mailtrap Email Sandbox](https://mailtrap.io/) を使用しています。  
リポジトリには個人の認証情報を含めていないため、クローン後にメール機能を使用する場合は以下の手順で設定してください。  

**① Mailtrapアカウントを作成し、認証情報を取得する**  
  
**② `.env` に以下を追記する**  
MAIL_MAILER=smtp  
MAIL_HOST=sandbox.smtp.mailtrap.io  
MAIL_PORT=2525  
MAIL_USERNAME=（MailtrapのUSERNAME）  
MAIL_PASSWORD=（MailtrapのPASSWORD）  
MAIL_FROM_ADDRESS="hello@example.com"  
MAIL_FROM_NAME="${APP_NAME}"  
MAILTRAP_SANDBOX_URL=（個人URL）  
  
**③ 設定を反映する　［PHPコンテナ内］**
  
```bash
php artisan config:clear  
```
  
---
  
## 動作確認
  
### アクセス先
  
| ロール | URL |
|--------|-----|
| 会員登録 | http://localhost/register |
| 一般ユーザー ログイン | http://localhost/login |
| 管理者 ログイン | http://localhost/admin/login |
  
### テスト用アカウント
  
| ロール | name | email | password |
|--------|------|-------|----------|
| 管理者 | admin user | admin@example.com | adminpass |
| 一般ユーザー | test user | user@example.com | password |
  
### 画面フロー
  
**一般ユーザー**
  
- 登録：会員登録 → メール認証 → 勤怠登録画面  
- 申請①：勤怠一覧 → 詳細 → 申請画面（承認待ち中は申請不可）  
- 申請②：申請一覧（承認待ち・承認済） → 詳細 → 申請画面  
  
**管理者**
  
- ①勤怠情報一覧 → 詳細 → 申請画面  
- ②スタッフ一覧 → 各スタッフの勤怠一覧 → 詳細 → 申請画面  
- ③申請一覧 → 詳細 → 承認画面 → 承認ボタンで承認済に変更  
  
---
  
## 注意事項
  
### 複数ロールの同時ログインについて
  
同一ブラウザでユーザーと管理者を同時にログインすると **419エラー** が発生します。  
Laravelのセッション構造上、同一ブラウザでは1セッションのみ有効です。  
  
| ロール | 推奨ブラウザ |
|--------|-------------|
| 一般ユーザー | 通常ブラウザ |
| 管理者 | シークレットモード または 別ブラウザ |
  
---
  
## テスト環境の構築
  
### 1. テスト用データベースの作成
  
MySQLコンテナに root ユーザーでログインし、`demo_test` データベースを作成する。  
  
### 2. `.env.testing` の作成　［PHPコンテナ内］  
  
```bash
cp .env .env.testing  
```
  
`.env.testing` を以下の内容に編集する：  
APP_ENV=test  
APP_KEY=            # 次のステップで生成  
DB_DATABASE=demo_test  
DB_USERNAME=root  
DB_PASSWORD=root  
MAILTRAP_SANDBOX_URL="https://example.test/"  
  
### 3. テスト用キーの生成とマイグレーション　［PHPコンテナ内］  
  
```bash
php artisan key:generate --env=testing
php artisan migrate --env=testing
```
  
### 4. phpunit.xml の確認  
  
以下の環境変数が設定されていることを確認する：  
  
```xml
<env name="DB_CONNECTION" value="mysql_test"/>  
<env name="DB_DATABASE" value="demo_test"/>  
```
  
### 5. テストの実行　［PHPコンテナ内］  
  
```bash
php artisan test  
```  
  
> **Viteのビルドについて**  
> テスト実行時に `public/build/manifest.json` が必要ですが、本プロジェクトはtesting環境でViteを読み込まない構成のため、`npm run build` は不要です。
  
---
  
## ER図・データベース設計

![ER図](mock-case2.png)
