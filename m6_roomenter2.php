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
$sql_join = $pdo->prepare("INSERT IGNORE INTO room_members (room_id, user_id) VALUES (:room_id, :user_id)");
$sql_join->bindParam(':room_id', $room_id, PDO::PARAM_INT);
$sql_join->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$sql_join->execute();

// メッセージ投稿処理
if (!empty($_POST['content'])) {
    $content = trim($_POST['content']);
    if ($content !== "") {
        $sql_post = $pdo->prepare("INSERT INTO room_posts (room_id, user_id, content) VALUES (:room_id, :user_id, :content)");
        $sql_post->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $sql_post->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $sql_post->bindParam(':content', $content, PDO::PARAM_STR);
        $sql_post->execute();
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?room_id=$room_id");
        exit;
    }
}

// メッセージ一覧取得
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
<title><?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?> ルーム</title>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($room['name'], ENT_QUOTES); ?> ルーム</h1>
        <p>ようこそ、<?php echo htmlspecialchars($user_name, ENT_QUOTES); ?> さん！</p>
    </header>

    <hr>

    <!-- ★★★ここから追加: ルーム内メニュー★★★ -->
    <nav>
        <h2>ルームメニュー</h2>
        <ul>
            <!-- リンクにルームIDを付与して、ルーム専用ページに移動できるようにする -->
            <li><a href="m6_board.php?room_id=<?php echo $room_id; ?>">プロフィール・質問集を見る</a></li>
            <li><a href="m6_post.php?room_id=<?php echo $room_id; ?>">プロフィール・質問集を投稿する</a></li>
            <li><a href="m6_picture chain.php?room_id=<?php echo $room_id; ?>">写真しりとりゲーム</a></li>
        </ul>
    </nav>
    <!-- ★★★ここまで追加★★★ -->
    
    <hr>

    <main>
        <!-- ★★★名称変更: 「投稿」から「メッセージ」へ★★★ -->
        <section id="message-board">
            <h2>メッセージ</h2>
            
            <!-- メッセージ投稿フォーム -->
            <form method="post">
                <textarea name="content" rows="4" cols="50" placeholder="ここにメッセージを入力" required></textarea><br>
                <input type="submit" value="メッセージを送信">
            </form>

            <hr>

            <!-- メッセージ一覧 -->
            <h3>メッセージ一覧</h3>
            <?php if (empty($posts)): ?>
                <p>まだメッセージはありません。</p>
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
        </section>
    </main>

    <hr>

    <footer>
        <p><a href="m6_rooms.php">他のルームを探す（ルーム一覧に戻る）</a></p>
        <p><a href="m6_logout.php">ログアウト</a></p>
    </footer>

</body>
</html>