<?php 
session_start();
// セッションを削除 
$_SESSION = array(); 

// セッションを終了 
session_destroy();

// ログイン画面へ戻る 
header("Location: login.php");
exit;
?>

<html>
    <p>ログアウトしました！</p>
</html>