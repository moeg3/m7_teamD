<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'あなたはまだログインしていません';
    header('Location: ../auth/login.php');
    exit;
}

/*
 * ログイン後にログイン担当が
 * $_SESSION['user_id']
 * $_SESSION['user_name']
 * をセットする想定
 */

require_once '../includes/my_db.php';
require_once '../includes/functions.php';

// ※ログイン機能実装後は $_SESSION['user_id'] などから取得します
$current_user_id = 1; 

// 最新の作品をすべて取得（実際のテーブル構造に合わせて調整してください）
$stmt = $pdo->query("SELECT * FROM works ORDER BY created_at DESC");
$all_works = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ログイン担当がまだ未完成なので、今は仮の値を入れる
$user_id = $_SESSION['user_id'] ?? 1;
$user_name = $_SESSION['user_name'] ?? 'ユーザー1';

require_once '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ホーム</title>
        <link rel="stylesheet" href="../css/index_style.css">
    </head>
    <body>
        <main class="home-page">
            
            <!-- ユーザー情報 --> 
            <section class="welcome-area">
                <h1>ホーム</h1>
                <!-- ログインしたユーザーの名前 -->
                <p>
                    ようこそ、<span class="user-name"> <?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?> </span>さん
                </p>
            </section>
            
            <!--投稿・編集-->
            <nav class="home-menu">
                <a href="workspace.php" class="menu-button"> 🎨 作品を作る </a>
                <a href="mypage.php" class="menu-button"> 🖼️ マイページ </a>
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
                
                <?php foreach ($all_works as $work): ?>
                <?php render_work_card($work, $current_user_id); ?>
                <?php endforeach; ?>
                <?php endif; ?>
                
                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <?php foreach ($all_works as $work): ?>
                    <!-- 関数を呼び出すだけでカードが生成される -->
                    <?php render_work_card($work, $current_user_id); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>