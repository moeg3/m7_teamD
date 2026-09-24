<?php

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('ログインが必要です');
}
$user_name = $_SESSION['user_name'];
?>
<header>
    <h1 class="title">推しクラ</h1>
    <nav class="nav">
        <ul class="menu">
            <li class="menu-item"><a href="/index.php">ホーム</a></li>
            <li class="menu-item"><a href="/mypage.php">マイページ</a></li>
            <li class="menu-item"><a href="/workspace.php">新規ワークスペース</a></li>
            <li class="user-item"><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></li>
            <li class="user-item"><a href="/auth/logout.php">ログアウト</a></li>
        </ul>
    </nav>
</header>