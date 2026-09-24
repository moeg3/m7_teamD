<?php
session_start();

// ★
require_once '/var/www/includes/db.php';
require_once '/var/www/includes/functions.php';

$workId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$workId) {
    http_response_code(400);
    exit('作品IDが不正です');
}

$workStmt = $pdo->prepare(
    'SELECT *
     FROM works
     WHERE id = :id'
);

$workStmt->execute([
    ':id' => $workId
]);

$work = $workStmt->fetch(PDO::FETCH_ASSOC);

if (!$work) {
    http_response_code(404);
    exit('作品が見つかりません');
}

$itemStmt = $pdo->prepare(
    'SELECT
        parts.parts_name,
        parts.price,
        items.part_id
     FROM items
     INNER JOIN parts
        ON parts.id = items.part_id
     WHERE items.user_id = :user_id
       AND items.work_id = :work_id
     ORDER BY items.id'
);

$itemStmt->execute([
    ':user_id' => $work['user_id'],
    ':work_id' => $work['work_id']
]);

$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

$partSummary = [];
$totalPrice = 0;

foreach ($items as $item) {
    $partId = (int)$item['part_id'];
    $price = (int)$item['price'];

    if (!isset($partSummary[$partId])) {
        $partSummary[$partId] = [
            'name' => $item['parts_name'],
            'price' => $price,
            'count' => 0,
        ];
    }

    $partSummary[$partId]['count']++;
    $totalPrice += $price;
}

$title = htmlspecialchars(
    $work['title'],
    ENT_QUOTES,
    'UTF-8'
);

$imagePath = htmlspecialchars(
    $work['thumbnail_path'],
    ENT_QUOTES,
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- ★ヘッダー表示 -->
    <?php require_once '/var/www/includes/header.php'; ?>

    <main>
        <h2><?= $title ?></h2>

        <img
            src="<?= $imagePath ?>"
            alt="<?= $title ?>"
            style="max-width: 600px; width: 100%;"
        >

        <h3>使用パーツ</h3>

        <?php if ($partSummary === []): ?>
            <p>使用パーツはありません。</p>
        <?php else: ?>
            <ul>
                <?php foreach ($partSummary as $part): ?>
                    <li>
                        <?= htmlspecialchars($part['name'], ENT_QUOTES, 'UTF-8') ?>
                        × <?= $part['count'] ?>個
                        （<?= number_format($part['price']) ?>円/
                        個）
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h3>
            合計金額:
            <?= number_format($totalPrice) ?>円
        </h3>
    </main>
</body>
</html>