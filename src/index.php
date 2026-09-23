<?php
session_start();

/*
 * ログイン後にログイン担当が
 * $_SESSION['user_id']
 * $_SESSION['user_name']
 * をセットする想定
 */

require_once '/var/www/includes/db.php';
require_once '/var/www/includes/functions.php';

// ※ログイン機能実装後は $_SESSION['user_id'] などから取得します
$current_user_id = 1; 

// 最新の作品をすべて取得（実際のテーブル構造に合わせて調整してください）
$stmt = $pdo->query("SELECT * FROM works ORDER BY created_at DESC");
$all_works = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ログイン担当がまだ未完成なので、今は仮の値を入れる
$user_id = $_SESSION['user_id'] ?? 1;
$user_name = $_SESSION['user_name'] ?? 'ユーザー1';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <h1>ホーム</h1>

    <!-- ログインしたユーザーの名前 -->
    <p>
        ようこそ、<?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?>さん
    </p>

    <!-- 投稿・編集 -->
    <div>
        <a href="workspace.php">投稿</a>
        <a href="mypage.php">編集</a>
    </div>

    <hr>

    <h2>ユーザーの作品</h2>
    
    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <?php foreach ($all_works as $work): ?>
            <!-- 関数を呼び出すだけでカードが生成される -->
            <?php render_work_card($work, $current_user_id); ?>
        <?php endforeach; ?>
    </div>

</body>
</html>