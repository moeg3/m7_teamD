<?php
// 自分で作成したキャンバスを一覧表示するページ
require_once '/var/www/includes/functions.php';

// $works にはデータベースから取得した作品一覧が入っていると仮定
// foreach ($works as $work) {
//     // ログイン中のユーザーID（例: セッションから取得）を渡すだけで、関数が勝手に判断してくれます
//     echo render_work_card($work, $_SESSION['user_id']); 
// }
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
    </body>
</html>