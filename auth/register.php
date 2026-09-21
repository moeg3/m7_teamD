<?php
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_name VARCHAR(50) NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>初回登録</title>
        <link rel="stylesheet" href="m6_style.css">
    </head>
    <body>
        <h1>初回ユーザー登録</h1>

        <form action="" method="post">
            <p>ユーザーネーム</p>
            <input name="user_name" type="text">
            <p>パスワード</p>
            <input name="password" type="password">

            <br />
            <input name="submit" type="submit" value="登録">
        </form>
    </body>
</html>