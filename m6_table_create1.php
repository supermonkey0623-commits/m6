<?php
session_start();
// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//users テーブル（ユーザー登録・ログイン用）
$sql= "CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(64) NOT NULL,
  email VARCHAR(255) UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,       -- 画像ファイル名やパスを保存する場合
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$pdo->exec($sql);

//posts テーブル（掲示板投稿用）あとで

//pictures テーブル（写真しりとりゲーム用）あとで

//rooms テーブル
$sql = "CREATE TABLE IF NOT EXISTS rooms (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(255) NOT NULL UNIQUE,
 owner_id INT UNSIGNED NOT NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 -- ★追加: owner_id は users テーブルの id を参照します
 FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$pdo->exec($sql);

echo "rooms テーブルを作成しました。<br>";

//ユーザとルームの関係 テーブル
$sql_room_members = "CREATE TABLE IF NOT EXISTS room_members (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 room_id INT UNSIGNED NOT NULL,
 user_id INT UNSIGNED NOT NULL,
 joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY(room_id, user_id),
 -- ★追加: room_id は rooms テーブルの id を、user_id は users テーブルの id を参照します
 FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$pdo->exec($sql_room_members);

echo "room_members テーブルを作成しました。<br>";

//投稿テーブル（既存掲示版投稿をルームごとに紐づけ）
$sql_room_posts = "CREATE TABLE IF NOT EXISTS room_posts (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 room_id INT UNSIGNED NOT NULL,
 user_id INT UNSIGNED NOT NULL,
 content TEXT NOT NULL,
 created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
 -- ★追加: room_id と user_id がそれぞれ親テーブルを参照します
 FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$pdo->exec($sql_room_posts);

echo "room_posts テーブルを作成しました。<br>";



echo "すべてのテーブルの準備が完了しました。";

?>


