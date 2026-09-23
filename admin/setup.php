<?php
// 管理者専用ページ
// http://localhost/handmade_app/admin/setup.phpからデータベース内にテーブルを作成

// 保存している各自のフォルダ構造からパス名を変更してください
require_once '/var/www/includes/db.php';

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

    // 2. parts テーブルの作成
    $sql_parts = "
        CREATE TABLE IF NOT EXISTS parts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            parts_name VARCHAR(100) NOT NULL,
            price INT NOT NULL DEFAULT 0,
            width_mm DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            height_mm DECIMAL(5,2) NOT NULL DEFAULT 0.00,
            image_path VARCHAR(255),
            url_link TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
    ";
    $pdo->exec($sql_parts);
    echo "partsテーブルの作成完了。<br>";


    // 3. items テーブルの作成
    $sql_items = "
        CREATE TABLE IF NOT EXISTS items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            part_id INT NOT NULL,
            work_id INT NOT NULL,
            x_set INT NOT NULL DEFAULT 0,
            y_set INT NOT NULL DEFAULT 0,
            rotation INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE CASCADE,
            INDEX idx_user_work (user_id, work_id)
        )
    ";
    $pdo->exec($sql_items);
    echo "itemsテーブルの作成完了。<br>";

    echo "<b>すべてのセットアップが完了しました！</b>";

} catch (PDOException $e) {
    die("テーブル作成エラー: " . $e->getMessage());
}
?>
<DOCTYPE html>
<html lang="ja">
    <body>
        <a href="admin.php">管理者ページに戻る</a>
    </body>
</html>