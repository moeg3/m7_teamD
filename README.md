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
