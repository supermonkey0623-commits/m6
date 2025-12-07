<?php
session_start();

// ログインしてなければログインページへ強制移動
if (empty($_SESSION['user_id'])) {
    header("Location: m6_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>掲示板トップ</title>
</head>
<body>
  <h1>掲示板トップページ</h1>
  <p>ようこそ、<?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES); ?> さん！</p>

  <p><a href="m6_board.php">掲示板へ</a></p>
  <p><a href="m6_post.php">投稿ページへ</a></p>
  <p><a href="m6_picture chain.php">写真しりとりゲーム</a></p>
  <p><a href="m6_logout.php">ログアウト</a></p>

</body>
</html>
