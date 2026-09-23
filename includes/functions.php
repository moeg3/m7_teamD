<?php
// includes/functions.php

/**
 * 作品一覧のカード（枠）のHTMLを生成する関数
 * 
 * @param array $work 作品のデータ配列（DBから取得した1件分のデータ）
 * @param int|null $current_user_id 現在ログインしているユーザーのID（未ログイン時はnull）
 */
function render_work_card($work, $current_user_id) {
    // セキュリティ対策：表示する文字は必ずエスケープ（無害化）する
    $work_id = htmlspecialchars($work['id'], ENT_QUOTES, 'UTF-8');
    $owner_id = $work['user_id'];
    
    // HTMLの組み立て開始
    $html = '';

    // ① 作品の画像（サムネイル）を表示するエリア
    $html .= '';
    $html .= '';
    $html .= '</div>';

    $html .= '<h4>作品番号: ' . $work_id . '</h4>';
    
    // ▼ ここが分岐の心臓部 ▼
    if ($owner_id === $current_user_id) {
        // 自分の作品の場合（編集・削除ボタンを表示）
        $html .= '<a href="workspace.php?edit_id=' . $work_id . '" style="color: blue;">[編集]</a> ';
        $html .= '<a href="delete_work.php?id=' . $work_id . '" style="color: red;">[削除]</a>';
    } else {
        // 他人の作品、または未ログインの場合（閲覧のみ）
        $html .= '<a href="view.php?id=' . $work_id . '" style="color: green;">[詳細を閲覧]</a>';
    }
    
    $html .= '</div>';
    
    return $html;
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