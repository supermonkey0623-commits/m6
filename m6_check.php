<?php
// --- PHP処理をファイルの先頭にまとめる ---
session_start();

// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost;charset=utf8';
$user = 'ユーザ名';
$password = 'パスワード';
$errorMessage = ""; // エラーメッセージを格納する変数

// ★PDOオブジェクトを作成する
try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('データベース接続失敗。' . $e->getMessage());
}

// SQL実行
$sql = 'SHOW TABLES';
$result = $pdo->query($sql);

// 取得したテーブル名を表示・複数テーブルがあれば複数表示される
foreach ($result as $row) {
    echo $row[0] . '<br />';
}
echo "<hr>";

$sql = 'SHOW CREATE TABLE rooms';
$result = $pdo -> query($sql);
 
// 取得した SQL を表示 （指定したテーブルを CREATE しようと思った際の SQL）
foreach ($result as $row) {
  echo $row[1];
}
echo "<hr>";
?>