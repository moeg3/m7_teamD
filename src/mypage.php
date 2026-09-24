<?php
session_start();

// ★
require_once '/var/www/includes/functions.php';
require_once '/var/www/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('ログインが必要です');
}

$user_id = (int)$_SESSION['user_id'];

// 自分の作品だけを取得
$stmt = $pdo->prepare("SELECT * FROM works WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $user_id]);
$my_works = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>MyPage</title>
        <link rel="stylesheet" href="../asetts/css/style.css">
    </head>
    <body>
        // ★ヘッダー表示
        <?php require_once '/var/www/includes/header.php'; ?>

        <p>画面遷移完了！</p>
        <h2>マイページ（自分の作品）</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php foreach ($my_works as $work): ?>
                <?php render_work_card($work, $user_id); ?>
            <?php endforeach; ?>
        </div>
    </body>
</html>