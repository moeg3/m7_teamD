<?php
// 自分で作成したキャンバスを一覧表示するページ
require_once '../includes/functions.php';
require_once '../includes/my_db.php';

$current_user_id = 1;

// 自分の作品だけを取得
$stmt = $pdo->prepare("SELECT * FROM works WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $current_user_id]);
$my_works = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>MyPage</title>
        <link rel="stylesheet" href="../css/mypage_style.css">
    </head>
    <body>
       
        <main class="mypage">
            <h2 class="mypage-title">マイページ（自分の作品）</h2>
            
            <div class="works-list">
                <?php if (empty($my_works)): ?> 
                <div class="no-works"> 
                <p>まだ作品がありません。</p>
                </div>
                
                <?php else: ?>
                <?php foreach ($my_works as $work): ?>
                <?php render_work_card($work, $current_user_id); ?>
                <?php endforeach; ?>
                <?php endif; ?>
        　　</div>
        </main>　
    </body>
</html>