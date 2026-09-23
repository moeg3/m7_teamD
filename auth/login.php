<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログイン</title>
        <link rel="stylesheet" href="auth_style.css">
    </head>
    <body>
        <?php
        //データベースへアクセス
       require_once '../includes/db.php';
        
        //ログインフォームから受け取る
        $user_name=$_POST["user_name"];
        $password=$_POST["password"];
        
        //ユーザー名をユーザーから検索
        $spl= " SELECT* FROM USERS WHERE user_name=:user_name";
        
        $stmt= $pdo -> prepare($spl);
        $stmt -> bindValue(':user_name' , $user_name , PDO::PARAM_STR);
        $stmt -> execute();
        
        $user_data = $stmt -> fetch(PDO::FETCH_ASSOC);
        
        //ユーザーが存在し、パスワードが一致するか確認
        if($user_data && password_verify($password,$user_data["password"])){
            
            //ログイン情報を保存
            $_SESSION["user_id"]=$user_data["id"];
            $_SESSION["user_name"] = $user_data["user_name"];
            
            echo "ログインしました";
            echo "<br />";
            echo '<a href="../index (4).php">トップページへ</a>';
            
        }
        else{
            echo "ユーザー名またはパスワードが間違っています。";
        }
        ?>
        
    </body>
</html>
