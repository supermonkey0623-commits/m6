<?php
// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "データベースに接続しました。<br><br>";

    // --- テーブル削除処理 ---

    // 外部キー制約のチェックを一時的に無効化
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    echo "外部キー制約のチェックを無効化しました。<br>";

    // 存在する可能性のある全てのテーブルをリストアップ
    $tables = ['room_posts', 'room_members', 'rooms', 'users', 'posts'];

    foreach ($tables as $table) {
        // IF EXISTS をつけることで、テーブルが存在しない場合でもエラーにならない
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
        echo "`$table` テーブルを削除しました。<br>";
    }

    // 外部キー制約のチェックを再度有効化
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "外部キー制約のチェックを有効化しました。<br>";

    echo "<br><strong>すべてのテーブルが正常に削除されました！</strong>";

} catch (PDOException $e) {
    // 外部キーチェックを元に戻してからエラーを表示
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    die("エラーが発生しました: " . $e->getMessage());
}
?>

