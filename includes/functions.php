<?php
/**
 * 作品のカード（サムネイルと情報）のHTMLを生成する関数
 * 
 * @param array $work データベースから取得した作品1件分のデータ
 * @param int|null $current_user_id 現在ログインしているユーザーのID（未ログイン時はnull）
 */
function render_work_card($work, $current_user_id = null) {
    // この作品を作った人と、今見ている人が同じか（所有者か）を判定
    $is_owner = ($work['user_id'] === $current_user_id);
    
    // 安全に表示するためのエスケープ処理
    $work_id = htmlspecialchars($work['id'], ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars($work['title'] ?? '無題の作品', ENT_QUOTES, 'UTF-8');
    // ※一覧画面では激重なKonvaを動かさず、保存しておいた画像（サムネイル）を表示します
    $image_path = htmlspecialchars($work['thumbnail_path'] ?? 'assets/images/default.png', ENT_QUOTES, 'UTF-8');

    // カードのHTML構造を開始
    echo '<div class="work-card" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; width: 250px; border-radius: 8px;">';
    
    // サムネイル画像の表示
    echo '<img src="' . $image_path . '" alt="' . $title . '" style="width: 100%; height: auto; border-bottom: 1px solid #eee; margin-bottom: 10px;">';
    
    // タイトルの表示
    echo '<h3 style="font-size: 16px; margin: 0 0 10px 0;">' . $title . '</h3>';

    // ▼▼ ここが権限によるボタンの出し分け処理 ▼▼
    echo '<div class="card-actions" style="display: flex; align-items: stretch; gap: 10px;">';
    
    if ($is_owner) {
        // 自分の作品の場合（マイページなど）：編集と削除ボタンを表示
        echo '<a href="workspace.php?edit_id=' . $work_id . '" style="display: flex; flex: 1; align-items: center; justify-content: center; padding: 5px 10px; background-color: #007bff; color: white; text-align: center; text-decoration: none; border-radius: 4px; font-size: 14px;">編集</a>';
        
        echo '<form action="mypage.php" method="POST" onsubmit="return confirm(\'本当に削除しますか？\');" style="display: flex; flex: 1; margin: 0;">';
        echo '<input type="hidden" name="work_id" value="' . $work_id . '">';
        echo '<button type="submit" style="width: 100%; padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">削除</button>';
        echo '</form>';
        echo '<a href="view_work.php?id=' . $work_id . '" style="flex: 1; padding: 5px 10px; background-color: #6c757d; color: white; text-align: center; text-decoration: none; border-radius: 4px; font-size: 14px;">詳細を見る</a>';
    } else {
        // 他人の作品の場合（インデックスなど）：閲覧ボタンのみを表示
        echo '<a href="view_work.php?id=' . $work_id . '" style="padding: 5px 10px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">詳細を見る</a>';
    }
    
    echo '</div>'; // .card-actions を閉じる
    echo '</div>'; // .work-card を閉じる
}


// フォームの入力が埋まっているかどうかチェック
function validateRequired(
    array $values,
    array $labels,
    array &$errors
): bool {
    foreach ($values as $fieldName => $value) {
        if (trim((string)$value) === '') {
            $errors[$fieldName] =
                ($labels[$fieldName] ?? $fieldName) . 'を入力してください';
        }
    }

    return $errors === [];
}
?>