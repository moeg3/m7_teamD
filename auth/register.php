<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>register</title>
        <link rel="stylesheet" href="auth_style.css">
    </head> 
    
    <body>
        
        <?php
        //データベースに接続
        require_once '../includes/db.php';
        
        
        //新規登録処理
        $user_name=$_POST["user_name"];
        $password=$_POST["password"];
        
        //パスワードをハッシュ化
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        //// USERSテーブルに登録 
        $sql = "INSERT INTO USERS (user_name, password) VALUES (:user_name, :password)"; 
        $stmt = $pdo->prepare($sql); 
        
        $stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR); 
        $stmt->execute(); 
        
        echo "新規登録が完了しました。"; 
        echo "<br>"; 
        echo '<a href="login.html">ログイン画面へ</a>';
        ?>
        
        
    </body>
    
</html>