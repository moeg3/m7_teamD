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
        <link rel="stylesheet" href="/assets/css/header_style.css">
        <link rel="stylesheet" href="/assets/css/index_style.css">
    </head>

    <body>
        <!-- ★ヘッダー表示 -->
        <?php require_once '/var/www/includes/header.php'; ?>

        <main class="home-page">
            <!-- ユーザー情報 --> 
            <section class="welcome-area">
                <h1>ホーム</h1>

                <!-- ログインしたユーザーの名前 -->
                <p>
                    ようこそ、<span class="user-name"> <?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?> </span>さん
                </p>
            </section>

            <!-- 投稿・編集 -->
            <nav class="home-menu">
                <a href="workspace.php" class="menu-button"> 投稿 </a>
                <a href="mypage.php" class="menu-button"> 編集 </a>
            </nav>

            <hr>
            <section class="works-section">
                <h2 class="home-section-title">ユーザーの作品</h2>
                <div class="works-list">
                <?php
                if (empty($all_works)):
                ?> 
                <div class="no-works"> 
                    <p>まだ作品がありません。</p>
                    <p>最初の作品を作ってみましょう！</p> 
                </div> 
                <?php else: ?>
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <?php foreach ($all_works as $work): ?>
                        <!-- 関数を呼び出すだけでカードが生成される -->
                        <?php render_work_card($work, $user_id); ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                </div>
            </section>
        </main>
    </body>
</html>