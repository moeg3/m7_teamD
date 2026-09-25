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
        parts.url_link,
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
    $purchaseUrl = trim((string)($item['url_link'] ?? ''));
    $purchaseUrlIsValid = filter_var($purchaseUrl, FILTER_VALIDATE_URL)
        && in_array(
            strtolower((string)parse_url($purchaseUrl, PHP_URL_SCHEME)),
            ['http', 'https'],
            true
        );

    if (!isset($partSummary[$partId])) {
        $partSummary[$partId] = [
            'name' => $item['parts_name'],
            'price' => $price,
            'url' => $purchaseUrlIsValid ? $purchaseUrl : null,
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
    <link rel="stylesheet" href="/assets/css/header_style.css">
    <link rel="stylesheet" href="/assets/css/view_work_style.css">
</head>
<body>
    <!-- ★ヘッダー表示 -->
    <?php require_once '/var/www/includes/header.php'; ?>

    <main class="view-work-main">
        <h2><?= $title ?></h2>

        <img class="work-thumbnail"
            src="<?= $imagePath ?>"
            alt="<?= $title ?>"
        >

        <h3>使用パーツ</h3>

        <?php if ($partSummary === []): ?>
            <p>使用パーツはありません。</p>
        <?php else: ?>
            <ul class="parts-list">
                <?php foreach ($partSummary as $part): ?>
                    <li>
                        <?= htmlspecialchars($part['name'], ENT_QUOTES, 'UTF-8') ?>
                        × <?= $part['count'] ?>個
                        （<?= number_format($part['price']) ?>円/
                        個）
                        <?php if ($part['url'] !== null): ?>
                            <a
                                href="<?= htmlspecialchars($part['url'], ENT_QUOTES, 'UTF-8') ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="purchase-link"
                            >パーツを購入</a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p class="total-price">
            合計金額:
            <strong><?= number_format($totalPrice) ?>円</strong>
        </p>
    </main>
</body>
</html>