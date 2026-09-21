## テーブル定義
| テーブル名 | 役割とポイント |
| :-------- | :----- |
| `USERS` |ユーザーの基本情報を管理します。パスワードは生のままではなく、後ほどPHP側で暗号化して保存する仕組みにします。
| `ITEMS` |キャンバスに置かれたパーツを管理します。`user_id` があるおかげで、誰が置いたパーツなのかを一瞬で見分けられます。また、ミリ単位のサイズを扱う `width_mm` と `height_mm` には小数を保存できる型（DECIMAL）を設定します。|

### USERS テーブル（ユーザー情報）

| カラム名 | データ型 | キー | 説明 |
| :--- | :--- | :--- | :--- |
| `id` | INT | PK (主キー) | ユーザーの固有ID（自動連番） |
| `user_name` | VARCHAR(50) | - | ユーザーの表示名 |
| `password` | VARCHAR(255) | - | パスワード（暗号化して保存するため長めの枠を確保） |


### ITEMS テーブル（キャンバス配置データ）

| カラム名 | データ型 | キー | 説明 |
| :--- | :--- | :--- | :--- |
| `id` | INT | PK (主キー) | 配置データの固有ID（自動連番） |
| `user_id` | INT | FK (外部キー)| 誰の配置データか（USERSテーブルのidと紐付け） |
| `name` | VARCHAR(100)| - | パーツの名称 |
| `price` | INT | - | 価格（合計計算のため数字のみ保存） |
| `width_mm` | DECIMAL(5,2)| - | パーツの横幅（例: 12.50mm のように小数を保存） |
| `height_mm`| DECIMAL(5,2)| - | パーツの縦幅（例: 12.50mm のように小数を保存） |
| `x_set` | INT | - | キャンバス上のX座標 |
| `y_set` | INT | - | キャンバス上のY座標 |
| `image_path`| VARCHAR(255)| - | サーバーに保存した画像のファイル名 |
| `shop_url` | TEXT | - | 通販サイトの購入先リンクURL |

### データベース構造（ER図）

```mermaid
%%{init: {'er': {'layoutDirection': 'LR'}}}%%
erDiagram
    USERS ||--o{ ITEMS : "配置する (1対多)"

    USERS {
        INT id PK "ユーザーの背番号 (主キー)"
        VARCHAR user_name "ユーザー名"
        VARCHAR password "パスワード"
    }

    ITEMS {
        INT id PK "配置パーツの背番号 (主キー)"
        INT user_id FK "誰が配置したか (USERSのid)"
        VARCHAR name "パーツ名"
        INT price "価格"
        DECIMAL width_mm "横幅 (mm)"
        DECIMAL height_mm "縦幅 (mm)"
        INT x_set "X座標"
        INT y_set "Y座標"
        VARCHAR image_path "画像ファイル名"
        TEXT shop_url "購入先リンク"
    }
