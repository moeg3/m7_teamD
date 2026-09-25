<?php
// ★保存している各自のフォルダ構造からパス名を変更してください
require_once '/var/www/includes/db.php';
require_once '/var/www/includes/functions.php';

$errors = [];
$imagePath = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_FILES['image_file']) ||
        $_FILES['image_file']['error'] !== UPLOAD_ERR_OK
    ) {
        $errors['image_file'] = '画像ファイルを選択してください';
    } else {
    $allowedMimeTypes = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    $mimeType = mime_content_type($_FILES['image_file']['tmp_name']);

    if (!isset($allowedMimeTypes[$mimeType])) {
        $errors['image_file'] = 'PNG、JPG、GIF、WebPのみアップロードできます';
    } else {
        $uploadDirectory = '/var/www/html/assets/images/parts/';
        $fileExtension = $allowedMimeTypes[$mimeType];

        $fileNumber = 1;

        do {
            $fileName = 'parts' . str_pad((string)$fileNumber, 2, '0', STR_PAD_LEFT)
                . '.' . $fileExtension;
            $serverPath = $uploadDirectory . $fileName;
            $fileNumber++;
        } while (file_exists($serverPath));

        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $serverPath)) {
            $errors['image_file'] = '画像の保存に失敗しました';
        } else {
            $imagePath = '/assets/images/parts/' . $fileName;
        }
        }
    }

    $partsName = trim($_POST['parts_name'] ?? '');
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
        if ($imagePath !== '') {
        unlink('/var/www/html' . $imagePath);
        }

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
        $stmt->bindValue(':image_path', $imagePath, PDO::PARAM_STR);
        $stmt->bindParam(':url_link', $urlLink, PDO::PARAM_STR);

        $stmt->execute();

        echo 'データベースに保存しました！';
    }
}$embed_url = "https://docs.google.com/document/d/e/2PACX-1vR_qn_L4B6ix40EO5wJvFijOXxpXePe2XLmsGdaqixaiN2u_Z5JqPQVBatpvQxLCzLHL9FNOMN8aNYm/pub";
?>

<DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>管理者用ページ</title>
        <link rel="stylesheet" href="../assets/css/admin_style.css">
    </head>
    <body>
        <div class="admin-page">            
                <h1>データベースを作成</h1>
            <div class="setup-area">
                <p>最初の一度だけ作成してください。</p>
                <a href="setup.php">作成</a>
            </div>

            <h2>パーツをデータベースに保存</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <p>パーツ名</p>
                <input type="text" name="parts_name">

                <p>画像パス(.png/.jpeg/.gif/.webp形式のみ)</p>
                <input type="file" name="image_file" accept="image/png,image/jpeg,image/gif,image/webp">

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

            <div class="transpage-item">
                <h1>サービス画面へ移動</h1>
                    <h2>まだログインしていない方はこちら</h2>
                        <a href="../auth/register.php">新規登録ページへ</a>
                    <h2>ログインしたことのある方はこちら</h2>
                        <a href="../auth/login.php">ログインページへ</a>
            </div>
        </div>
        <iframe src="<?php echo htmlspecialchars($embed_url, ENT_QUOTES, 'UTF-8'); ?>" 
                width="90%" 
                height="100%" 
                style="border: none; 
                       display: block;
                       margin: 0 auto;">
    </body>
</html>