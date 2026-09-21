// データベースに接続するときに使用
// データベースを使いたいときはphp内でrequire_once 'includes/db.php';を記述
<?php
    // DB接続
    // 自分のDBのdsn・user名・パスワードに変更
    $dsn = 'mysql:dbname=my_database;host=db;charset=utf8mb4';
    $user = 'root';
    $password = getenv('MYSQL_ROOT_PASSWORD') ?: 'root';

    //PDOとはPHP Data Objectsの略称で、データベースへの接続機能を提供するクラスライブラリ
    $pdo = new PDO(
        $dsn,
        $user,
        $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)
    );
?>