<?php
// データベース接続と共通ヘッダーの読み込み
require_once '/var/www/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 送られてきたJSONデータを読み込む
    $json = file_get_contents('php://input');
    $items = json_decode($json, true);

    if ($items !== null) {
        try {
            // ※現在はログイン機能がないため、ユーザーID: 1 を仮に使用します
            $user_id = 1;

            $pdo->beginTransaction();

            $workStmt = $pdo->prepare(
                'SELECT COALESCE(MAX(work_id), 0) + 1
                 FROM items
                 WHERE user_id = :user_id'
            );

            $workStmt->execute([
                ':user_id' => $user_id
            ]);

            $work_id = (int)$workStmt->fetchColumn();

            $itemStmt = $pdo->prepare(
                'INSERT INTO items
                (user_id, part_id, work_id, x_set, y_set, rotation)
                VALUES
                (:user_id, :part_id, :work_id, :x_set, :y_set, :rotation)'
            );
            
            foreach ($items as $item) {
                $itemStmt->execute([
                    ':user_id' => $user_id,
                    ':part_id' => (int)$item['partId'],
                    ':work_id' => $work_id,
                    ':x_set' => (int)$item['x'],
                    ':y_set' => (int)$item['y'],
                    ':rotation' => (int)$item['rotation']
                ]);
            }

            $pdo->commit();
            
            // JavaScript側に成功メッセージだけを返して、ここでPHPを強制終了（exit）する
            // ※これをしないと、この後のHTML（画面）まで一緒に裏側で送られてしまいます
            echo count($items) . " 件のパーツデータをデータベースに記録しました。";
            exit; 
            
        } catch (PDOException $e) {
            echo "データベースエラー: " . $e->getMessage();
            exit;
        }
    }
}

$stmt = $pdo->query("SELECT * FROM parts ORDER BY id ASC");
$parts_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '/var/www/includes/header.php';
?>
<main>
    <h2>作業キャンバス</h2>

    <!-- flexboxを使って左右に並べる -->
    <div style="display: flex; gap: 20px;">

        <!-- 左側：キャンバスエリア -->
        <div id="canvas-container" style="background-color: #f0f0f0; width: 100%; max-width: 800px; height: 500px; border: 1px solid #ccc; margin: 0 auto;"></div>
        
        <!-- 右側：パーツのパレットエリア -->
        <div id="palette" style="width: 150px; background-color: #fff; border: 1px solid #ccc; padding: 10px;">
            <p>パーツ一覧</p>
            <?php foreach ($parts_list as $part): ?>
                <img class="drag-item" 
                    src="<?= htmlspecialchars($part['image_path'], ENT_QUOTES, 'UTF-8') ?>" 
                    data-image-path="<?= htmlspecialchars($part['image_path'], ENT_QUOTES, 'UTF-8') ?>"
                    data-size-mm="<?= htmlspecialchars($part['width_mm'], ENT_QUOTES, 'UTF-8') ?>" 
                    data-part-id="<?= htmlspecialchars($part['id'], ENT_QUOTES, 'UTF-8') ?>" 
                    title="<?= htmlspecialchars($part['parts_name'], ENT_QUOTES, 'UTF-8') ?> - ¥<?= htmlspecialchars($part['price'], ENT_QUOTES, 'UTF-8') ?>"
                    draggable="true" 
                    style="width: 50px; height: 50px; object-fit: contain; margin-bottom: 10px; cursor: grab; border: 1px solid #eee;">
            <?php endforeach; ?>
        </div>
    </div>

    <div style="margin-top: 15px; text-align: center;">
        <button id="save-btn" style="padding: 10px 30px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">作品を保存する</button>
    </div>

    <!-- ドラッグ＆ドロップを簡単に実装できるライブラリ（Konva.js）を読み込む -->
    <script src="https://unpkg.com/konva@9.3.1/konva.min.js"></script>
    <!-- メインの処理を書くJSファイル -->
    <script src="assets/js/canvas.js"></script>
</body>
</main>