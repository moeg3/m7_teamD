<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ヘッダー</title>
        <link rel="stylesheet" href="../css/workspace_style.css">
        <link rel="stylesheet" href="../css/heder_style.css">
    </head>    
    
    <?php
    
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }
    if (!isset($_SESSION['user_id'])){
        http_response_code(401);
        echo '<p>ログインが必要です。</p>';
        echo '<p><a href="../auth/login.php">ログインページへ</a></p>';
        exit; 
        
    }
    
    $user_name = $_SESSION['user_name'];
    ?>
    
    <header>
        <h1 class="title">推しクラ</h1>
        <nav class="nav">
            <ul class="menu">
                <li class="menu-item"><a href="../src/index (4).php">ホーム</a></li>
                <li class="menu-item"><a href="../src/mypage.php">マイページ</a></li>
                <li class="menu-item"><a href="../src/workspace.php">新規ワークスペース</a></li>
                <li class="user-item"><?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?></li>
                <li class="user-item logout"><a href="../auth/logout.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>
</html>