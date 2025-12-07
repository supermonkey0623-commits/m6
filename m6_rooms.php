<?php
// --- PHP処理をファイルの先頭にまとめる ---
session_start();

// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';
$errorMessage = ""; // エラーメッセージを格納する変数

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // DB接続エラーの場合はここで処理を終了
    die("データベース接続に失敗しました: " . $e->getMessage());
}

// ログインチェック
if (empty($_SESSION['user_id'])) {
    header("Location: m6_login.php");
    exit;
}

// ログインユーザーの情報を取得
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'ゲスト'; // ログイン時に'user_name'もセッションに保存されている想定

// 新しいルーム作成処理
// ★修正点1: POSTされるキーを 'name' に変更
if (!empty($_POST['name'])) {
    $room_name = trim($_POST['name']);
    if ($room_name !== "") {
        
        // ★追加: 同じ名前のルームが既に存在するかチェック
        $check_sql = $pdo->prepare("SELECT id FROM rooms WHERE name = :name LIMIT 1");
        $check_sql->bindParam(':name', $room_name, PDO::PARAM_STR);
        $check_sql->execute();
        $existing_room = $check_sql->fetch();

        if ($existing_room) {
            // 既に存在する場合、エラーメッセージをセット
            $errorMessage = "エラー: そのルーム名は既に使用されています。";
        } else {
            // ★修正点2: INSERTするカラムを 'name' と 'owner_id' に変更
            $sql = $pdo->prepare("INSERT INTO rooms(name, owner_id) VALUES (:name, :owner_id)");
            $sql->bindParam(':name', $room_name, PDO::PARAM_STR);
            $sql->bindParam(':owner_id', $user_id, PDO::PARAM_INT);
            $sql->execute();
            
            // 処理後にページをリロードしてフォームの再送信を防ぐ
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// ルーム一覧を取得
$rooms = $pdo->query("SELECT * FROM rooms ORDER BY created_at DESC")->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ルーム一覧</title>
</head>
<body>
    <h1>ルーム一覧</h1>
    <!-- ★修正点3: ユーザーIDではなくユーザー名を表示 -->
    <p>ようこそ、<?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES); ?> さん！</p>

    <!-- 新しいルーム作成フォーム -->
    <h2>新しいルームを作成</h2>
    <?php
    // エラーメッセージがあれば表示
    if (!empty($errorMessage)) {
        echo "<p style='color:red;'>" . htmlspecialchars($errorMessage, ENT_QUOTES) . "</p>";
    }
    ?>
    <form method="post">
        <!-- ★修正点4: inputのname属性を 'name' に変更 -->
        <input type="text" name="name" placeholder="ルーム名" required>
        <input type="submit" value="作成">
    </form>

    <hr>

    <?php
    // ルーム一覧表示
    if ($rooms) {
        echo "<h2>既存のルーム</h2>";
        echo "<ul>";
        foreach ($rooms as $room) {
            echo "<li>";
            // ★修正点5: 表示するカラム名を 'name' に変更
            echo htmlspecialchars($room['name'], ENT_QUOTES);
            // ★修正点6: リンクで渡すパラメータ名を 'room_id' に変更
            echo " - <a href='m6_roomenter2.php?room_id=" . htmlspecialchars($room['id'], ENT_QUOTES) . "'>入室</a>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>まだルームがありません。上のフォームから作成してください。</p>";
    }
    ?>
</body>
</html>
