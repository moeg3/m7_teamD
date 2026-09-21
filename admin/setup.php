<?php
// 管理者専用ページ
// http://localhost/handmade_app/admin/setup.phpからデータベース内にテーブルを作成

require_once '../includes/db.php';
try {
    // 1. users テーブルの作成
    $sql_users = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_name VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    // exec()でクエリを実行
    $pdo->exec($sql_users);
    echo "usersテーブルの作成完了。<br>";

    // 2. items テーブルの作成
    $sql_items = "
        CREATE TABLE IF NOT EXISTS items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            price INT NOT NULL DEFAULT 0,
            width_mm DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            height_mm DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            x_set INT NOT NULL DEFAULT 0,
            y_set INT NOT NULL DEFAULT 0,
            image_path VARCHAR(255),
            shop_url TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ";
    $pdo->exec($sql_items);
    echo "itemsテーブルの作成完了。<br>";

    echo "<b>すべてのセットアップが完了しました！</b>";

} catch (PDOException $e) {
    die("テーブル作成エラー: " . $e->getMessage());
}
?>