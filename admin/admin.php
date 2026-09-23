<?php
// 保存している各自のフォルダ構造からパス名を変更してください
require_once '/var/www/includes/db.php';
require_once '/var/www/includes/functions.php';
$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partsName = trim($_POST['parts_name'] ?? '');
    $imagePath = trim($_POST['image_path'] ?? '');
    $widthMm = trim($_POST['width_mm'] ?? '');
    $heightMm = trim($_POST['height_mm'] ?? '');
    $urlLink = trim($_POST['url_link'] ?? '');
    $amount = trim($_POST['amount'] ?? '');

    $isValid = validateRequired(
        [
            'parts_name' => $partsName,
            'image_path' => $imagePath,
            'width_mm' => $widthMm,
            'height_mm' => $heightMm,
            'url_link' => $urlLink,
            'amount' => $amount,
        ],
        [
            'parts_name' => 'パーツ名',
            'image_path' => '画像パス',
            'width_mm' => '横幅',
            'height_mm' => '縦幅',
            'url_link' => '購入リンク',
            'amount' => '金額',
        ],
        $errors
    );

    if(!$isValid) {
        foreach ($errors as $error) {
            echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '<br>';
        }
    } else {
        $sql = "INSERT INTO parts
                (parts_name, price, width_mm, height_mm, image_path, url_link)
                VALUES
                (:parts_name, :amount, :width_mm, :height_mm, :image_path, :url_link)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':parts_name', $partsName, PDO::PARAM_STR);
        $stmt->bindValue(':amount', (int)$amount, PDO::PARAM_INT);
        $stmt->bindValue(':width_mm', (float)$widthMm);
        $stmt->bindValue(':height_mm', (float)$heightMm);
        $stmt->bindParam(':image_path', $imagePath, PDO::PARAM_STR);
        $stmt->bindParam(':url_link', $urlLink, PDO::PARAM_STR);

        $stmt->execute();

        echo 'データベースに保存しました！';
    }
}
?>

<DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>管理者用ページ</title>
    </head>
    <body>
        <h1>データベースを作成</h1>
        <p>最初の一度だけ作成してください。</p>
        <a href="setup.php">作成</a>

        <h2>パーツをデータベースに保存</h2>
        <form action="" method="post">
            <p>パーツ名</p>
            <input type="text" name="parts_name">
            <p>画像パス（フォルダ内の画像を保存している場所）</p>
            <input type="text" name="image_path">
            <h3>大きさ</h3>
                <p>横幅(mm)</p>
                <input type="number" name="width_mm" step="0.01" min="0">
                <p>縦幅(mm)</p>
                <input type="number" name="height_mm" step="0.01" min="0">
            <p>購入リンク</p>
            <input type="text" name="url_link">
            <p>金額</p>
            <input type="number" name="amount">
            <br />
            <input name="submit" type="submit" value="登録">
        </form>
    </body>
</html>