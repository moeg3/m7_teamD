<?php
session_start();

// ★
require_once '/var/www/includes/db.php';
require_once '/var/www/includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    echo 'あなたはまだログインしていません';
    header('Location: ../auth/login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

/*
 * ログイン後にログイン担当が
 * $_SESSION['user_id']
 * $_SESSION['user_name']
 * をセットする想定
 */

// 最新の作品をすべて取得
$stmt = $pdo->prepare(
        "SELECT *
         FROM works 
         WHERE user_id != :user_id
         ORDER BY created_at DESC"
);

$stmt->execute([
    ':user_id' => $user_id
]);

$all_works = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    // ★ヘッダー表示
    <?php require_once '/var/www/includes/header.php'; ?>

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
            <?php render_work_card($work, $user_id); ?>
        <?php endforeach; ?>
    </div>

</body>
</html>