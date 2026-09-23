<?php
// 自分で作成したキャンバスを一覧表示するページ
require_once '/var/www/includes/functions.php';
require_once '/var/www/includes/db.php';

$current_user_id = 1;

// 自分の作品だけを取得
$stmt = $pdo->prepare("SELECT * FROM works WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $current_user_id]);
$my_works = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>MyPage</title>
        <link rel="stylesheet" href="/var/www/asetts/css/style.css">
    </head>
    <body>
        <p>画面遷移完了！</p>
        <h2>マイページ（自分の作品）</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php foreach ($my_works as $work): ?>
                <?php render_work_card($work, $current_user_id); ?>
            <?php endforeach; ?>
        </div>
    </body>
</html>