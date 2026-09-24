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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $workId = filter_input(INPUT_POST, 'work_id', FILTER_VALIDATE_INT);

    if (!$workId) {
        http_response_code(400);
        exit('作品IDが不正です');
    }

    try {
        $pdo->beginTransaction();

        $workStmt = $pdo->prepare(
            'SELECT work_id, thumbnail_path
             FROM works
             WHERE id = :id AND user_id = :user_id'
        );
        $workStmt->execute([
            ':id' => $workId,
            ':user_id' => $user_id
        ]);
        $work = $workStmt->fetch(PDO::FETCH_ASSOC);

        if (!$work) {
            $pdo->rollBack();
            http_response_code(404);
            exit('作品が見つかりません');
        }

        $itemStmt = $pdo->prepare(
            'DELETE FROM items
             WHERE user_id = :user_id AND work_id = :work_id'
        );
        $itemStmt->execute([
            ':user_id' => $user_id,
            ':work_id' => $work['work_id']
        ]);

        $deleteStmt = $pdo->prepare(
            'DELETE FROM works
             WHERE id = :id AND user_id = :user_id'
        );
        $deleteStmt->execute([
            ':id' => $workId,
            ':user_id' => $user_id
        ]);

        $pdo->commit();

        $thumbnailPath = (string)($work['thumbnail_path'] ?? '');
        if (str_starts_with($thumbnailPath, 'assets/images/works/')) {
            $thumbnailFile = '/var/www/html/' . $thumbnailPath;
            if (is_file($thumbnailFile)) {
                unlink($thumbnailFile);
            }
        }

        header('Location: mypage.php');
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        exit('作品の削除に失敗しました');
    }
}

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
        <link rel="stylesheet" href="/assets/css/header_style.css">
        <link rel="stylesheet" href="/assets/css/mypage_style.css">
    </head>
    <body>
        <!-- ★ヘッダー表示 -->
        <?php require_once '/var/www/includes/header.php'; ?>

        <h2>マイページ（自分の作品）</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php foreach ($my_works as $work): ?>
                <?php render_work_card($work, $user_id); ?>
            <?php endforeach; ?>
        </div>
    </body>
</html>