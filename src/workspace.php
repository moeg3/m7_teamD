<?php
// データベース接続と共通ヘッダーの読み込み
require_once '../includes/db.php';
require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="ja">
    <main>
        <h2>作業キャンバス</h2>
        <!-- ここにパーツを配置するエリア -->
        <div id="canvas-container" style="background-color: #f0f0f0; width: 100%; max-width: 800px; height: 500px; border: 1px solid #ccc; margin: 0 auto;"></div>
    </main>
    <body>
        <!-- ドラッグ＆ドロップを簡単に実装できるライブラリ（Konva.js）を読み込む -->
        <script src="https://unpkg.com/konva@9.3.1/konva.min.js"></script>
        <!-- メインの処理を書くJSファイル -->
        <script src="assets/js/canvas.js"></script>
    </body>
</html>