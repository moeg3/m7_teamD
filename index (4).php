<?php
session_start();

/*
 * ログイン後にログイン担当が
 * $_SESSION['user_id']
 * $_SESSION['user_name']
 * をセットする想定
 */

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

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <h1>ホーム</h1>

    <!-- ログインしたユーザーの名前 -->
    <p>
        ようこそ、<?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?>さん
    </p>

    <!-- 投稿・編集 -->
    <div>
        <a href="post.php">投稿</a>
        <a href="edit.php">編集</a>
    </div>

    <hr>

    <h2>ユーザーの作品</h2>

    <!-- ユーザーごとの作品を見る -->
    <div>
        <p>
            <a href="user_items.php?user_id=1">
                ユーザー1の作品
            </a>
        </p>

        <p>
            <a href="user_items.php?user_id=2">
                ユーザー2の作品
            </a>
        </p>

        <p>
            <a href="user_items.php?user_id=3">
                ユーザー3の作品
            </a>
        </p>

        <p>
            <a href="user_items.php?user_id=4">
                ユーザー4の作品
            </a>
        </p>
    </div>

</body>
</html>