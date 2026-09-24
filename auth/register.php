<?php
session_start();
// ★データベースに接続
require_once '/var/www/includes/db.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){
    //新規登録処理
    $user_name=$_POST["user_name"] ??'';
    $password=$_POST["password"] ??'';
    $password_confirm = $_POST["password_confirm"] ?? '';
    
    if ($password !== $password_confirm){
        echo "パスワードが一致しません。"; 
        
    }
    else{
        //パスワードをハッシュ化
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        //// USERSテーブルに登録 
        $sql = "INSERT INTO users (user_name, password) VALUES (:user_name, :password)"; 
        $stmt = $pdo->prepare($sql); 
        
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR); 
        $stmt->execute(); 
        
        // 登録成功 → ログイン画面へ移動
        header("Location: login.php");
        exit;   
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>初回登録</title>
        <link rel="stylesheet" href="../assets/css/auth_style.css">
    </head>
    <body>
        <div class="form-container">
            <h1>初回ユーザー登録</h1>
            <?php
            if(isset($error)){
            echo "<p>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>";
            }
            ?>
            
            <form action="register.php" method="post">
                <label>ユーザーネーム</label>
                <input name="user_name" type="text" required>
                <label>パスワード</label>
                <input name="password" type="password"required>
                <label>パスワード確認</label>
                <input name="password_confirm" type="password" required>
                <br />
                <input name="submit" type="submit" value="登録">
            </form>
            
            <div class="form-link">    
                <p>アカウントを持っている方は</p>
                <a href="login.php">ログイン</a>
            </div>
        </div>
    </body>
</html>