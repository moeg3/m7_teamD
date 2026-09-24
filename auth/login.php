<?php
session_start();

//★データベースへアクセス
require_once '/var/www/includes/db.php';

if($_SERVER["REQUEST_METHOD"] === "POST"){
    //ログインフォームから受け取る
    $user_name=$_POST["user_name"] ??'';
    $password=$_POST["password"] ??'';
    
    //ユーザー名をユーザーから検索
    $sql= " SELECT* FROM users WHERE user_name=:user_name";
    
    $stmt= $pdo -> prepare($sql);
    $stmt -> bindValue(':user_name' , $user_name , PDO::PARAM_STR);
    $stmt -> execute();
    $user_data = $stmt -> fetch(PDO::FETCH_ASSOC);
    
    //ユーザーが存在し、パスワードが一致するか確認
    if($user_data && password_verify($password,$user_data["password"])){
        //ログイン情報を保存
        $_SESSION["user_id"]=$user_data["id"];
        $_SESSION["user_name"] = $user_data["user_name"];
        
        // ログイン成功 → トップページ画面へ移動
        header("Location: ../index.php");
        exit;
        
    }
    else{
        // ログイン失敗 
        $error = "ユーザー名またはパスワードが間違っています。";
        
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログイン</title>
        <link rel="stylesheet" href="../assets/css/auth_style.css">
    </head>
    <body>
        <div class="form-container">
            <h1>ログイン</h1>
            <?php
            if(isset($error)){
            echo "<p>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</p>";
            }
            ?>
            
            <form action="login.php" method="post">
                <label>ユーザーネーム</label>
                <input name="user_name" type="text" required>
                <label>パスワード</label>
                <input name="password" type="password" required>
                
                <input name="submit" type="submit" value="ログイン">
            </form>
            
            <div class="form-link">
                <p>アカウントを持っていない方は</p>
                <a href="register.php">新規登録</a>
            </div>
        </div>
    </body>
</html>