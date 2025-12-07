<?php
// --- PHP処理をファイルの先頭にまとめる ---
session_start();

// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// ログインチェック
if (empty($_SESSION['user_id'])) {
    header("Location: m6_login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'ゲスト';

// GETパラメータでルームID取得
if (empty($_GET['room_id'])) {
    die("エラー: ルームが指定されていません。");
}
$room_id = (int)$_GET['room_id'];

// ルーム情報取得
$sql_room = $pdo->prepare("SELECT * FROM rooms WHERE id = :room_id");
$sql_room->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$sql_room->execute();
$room = $sql_room->fetch();
if (!$room) {
    die("エラー: 指定されたルームは存在しません。");
}

// 自動入室処理（room_membersに登録）
// INSERT IGNORE なので、既にメンバーの場合は何も起こらない
$sql_join = $pdo->prepare("INSERT IGNORE INTO room_members (room_id, user_id) VALUES (:room_id, :user_id)");
$sql_join->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$sql_join->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$sql_join->execute();

// 投稿処理
if (!empty($_POST['content'])) {
    $content = trim($_POST['content']);
    if ($content !== "") {
        // ★修正点1: INSERT先のテーブルを 'room_posts' に指定
        $sql_post = $pdo->prepare("INSERT INTO room_posts (room_id, user_id, content) VALUES (:room_id, :user_id, :content)");
        $sql_post->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $sql_post->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $sql_post->bindParam(':content', $content, PDO::PARAM_STR);
        $sql_post->execute();
        
        // 投稿後に自身にリダイレクトしてフォームの再送信を防ぐ
        header("Location: " . $_SERVER['PHP_SELF'] . "?room_id=$room_id");
        exit;
    }
}

// ルーム内投稿取得
// ★修正点2: 投稿を取得するテーブルを 'room_posts' に指定
$sql_posts = $pdo->prepare("
    SELECT p.*, u.name AS user_name 
    FROM room_posts p 
    JOIN users u ON p.user_id = u.id 
    WHERE p.room_id = :room_id
    ORDER BY p.created_at ASC
");
$sql_posts->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$sql_posts->execute();
$posts = $sql_posts->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<!-- ★修正点3: ルーム名を表示するカラムを '$room['name']' に変更 -->
<title><?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?> ルーム</title>
</head>
<body>
<h1><?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?> ルーム</h1>
<p>ようこそ、<?php echo htmlspecialchars($user_name, ENT_QUOTES); ?> さん！</p>

<hr>

<!-- 投稿フォーム -->
<h2>投稿する</h2>
<form method="post">
    <textarea name="content" rows="4" cols="50" placeholder="ここにメッセージを入力" required></textarea><br>
    <input type="submit" value="投稿">
</form>

<hr>

<!-- 投稿一覧 -->
<h2>投稿一覧</h2>
<?php if (empty($posts)): ?>
    <p>まだ投稿はありません。</p>
<?php else: ?>
    <ul>
    <?php foreach ($posts as $post): ?>
        <li>
            <strong><?php echo htmlspecialchars($post['user_name'], ENT_QUOTES); ?>:</strong>
            <p style="margin: 5px 0 5px 20px;"><?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES)); ?></p>
            <small><?php echo $post['created_at']; ?></small>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<p><a href="m6_rooms.php">ルーム一覧に戻る</a></p>
<p><a href="m6_index.php">トップページへ戻る</a></p>

</body>
</html>