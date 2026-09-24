<?php
session_start();

// ★データベース接続
require_once '/var/www/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('ログインが必要です');
}

$user_id = (int)$_SESSION['user_id'];

// 作品完了ボタンを押したときの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 送られてきたJSONデータを読み込む
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data !== null && isset($data['thumbnail']) && isset($data['items'])) {
        $items = $data['items'] ?? [];
        $thumbnail = $data['thumbnail'] ?? '';

        try {
            $pdo->beginTransaction();

            // itemsテーブル内のwork_idを取得して設定
            $workIdStmt = $pdo->prepare(
                'SELECT COALESCE(MAX(work_id), 0) + 1
                 FROM items
                 WHERE user_id = :user_id'
            );

            $workIdStmt->execute([
                ':user_id' => $user_id
            ]);

            $work_id = (int)$workIdStmt->fetchColumn();

            // ★作品サムネイルの画像ファイルを保存
            $uploadDirectory = '/var/www/html/assets/images/works/';

            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0775, true);
            }

            preg_match('/^data:image\/png;base64,(.+)$/', $thumbnail, $matches);

            $imageData = base64_decode($matches[1], true);

            $fileName = 'user' . $user_id . 'work' . $work_id . '.png';
            $filePath = $uploadDirectory . $fileName;

            if (file_put_contents($filePath, $imageData) === false) {
                throw new RuntimeException('画像の保存に失敗しました');
            }

            // ★
            $thumbnail_path = 'assets/images/works/' . $fileName;

            // worksテーブル内にデータを追加
            $thumbStmt = $pdo->prepare(
                'INSERT INTO works
                (user_id, work_id, thumbnail_path)
                VALUES
                (:user_id, :work_id, :thumbnail_path)'
            );

            $thumbStmt->execute([
                ':user_id' => $user_id,
                ':work_id' => $work_id,
                ':thumbnail_path' => $thumbnail_path
            ]);

            // itemsテーブル内にデータを追加
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
            
            // JavaScript側に成功メッセージだけを返して、ここでPHPを強制終了（exit）
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
?>
<body>
    // ★ヘッダー表示
    <?php require_once '/var/www/includes/header.php'; ?>

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
    </main>
</body>