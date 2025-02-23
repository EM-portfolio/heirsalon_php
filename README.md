### プロジェクトの概要
PHPでヘアサロンの予約システムを作成しました。
このシステムでは、ユーザーが予約を簡単に行える機能を提供し、サロンの管理者が予約情報を効率的に管理できるようにしています。
Google APIを使用して管理者側のGoogleカレンダーに予約が登録されるようにしています。

GitHubにアップロードするため、APIキー等の機密情報は伏せています。
そのため、一部の機能は使用できないようにしています。

#### 使用技術
**バックエンド**
- ベース: PHP
- データベース: MySQL
- サーバー: Docker (開発環境のコンテナ化)

**フロントエンド**
- マークアップ: HTML5
- スタイリング: CSS3
- インタラクション: jQuery, AJAX

**実装機能**
- ログイン/ログアウト機能
- クーポン
- ajaxによる非同期確認ページ
- GoogleAPIによるカレンダーの動機
- fullCalenderを利用したわかりやすい予約表
- ユーザー管理画面
- 予約管理画面
- 会員情報変更画面

### 工夫したポイント（技術的にこだわった点、苦労した点など）

1. **ユーザーインターフェースの工夫**
  - AJAXを活用して確認ページを省略し、直感的に予約が行えるように設計しました。これにより、ユーザーの操作性が向上を目指しました。

2. **GoogleAPIの利用**
  - Webアプリケーション上のみならず、管理者がGoogleカレンダーを通じて予約確認もできるようにし、
  管理者側の自由も大切にしました。

3. **セキュリティ対策**
  - APIキーなどの機密情報をGitHubにアップロードしないように環境変数で管理しました。これにより、セキュリティを強化しつつ、コードの透明性を保ちました。
