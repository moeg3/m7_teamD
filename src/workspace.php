<?php
// データベース接続と共通ヘッダーの読み込み
require_once '../includes/db.php';
require_once '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="ja">
    <main>
        <h2>作業キャンバス</h2>
        <!-- flexboxを使って左右に並べます -->
        <div style="display: flex; gap: 20px;">
            <!-- 左側：キャンバスエリア -->
            <div id="canvas-container" style="background-color: #f0f0f0; width: 100%; max-width: 800px; height: 500px; border: 1px solid #ccc; margin: 0 auto;"></div>
            
            <!-- 右側：パーツの箱（パレット） -->
            <div id="palette" style="width: 150px; background-color: #fff; border: 1px solid #ccc; padding: 10px;">
                <p>パーツ一覧</p>
                <!-- draggable="true" をつけることでドラッグ可能になります -->
                <div class="drag-item" data-color="red" draggable="true" style="width: 50px; height: 50px; background: red; margin-bottom: 10px; cursor: grab;"></div>
                <div class="drag-item" data-color="blue" draggable="true" style="width: 50px; height: 50px; background: blue; margin-bottom: 10px; cursor: grab;"></div>
            </div>
    </main>
    <body>
        <!-- ドラッグ＆ドロップを簡単に実装できるライブラリ（Konva.js）を読み込む -->
        <script src="https://unpkg.com/konva@9.3.1/konva.min.js"></script>
        <!-- メインの処理を書くJSファイル -->
        <script src="assets/js/canvas.js"></script>

        
    </body>
</html>