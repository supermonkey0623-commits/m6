    <?php
    session_start();    
// ログインしてなければログインページへ強制移動
if (empty($_SESSION['user_id'])) {
    header("Location: m6_login.php");
    exit;
}
?>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ミッション6</title>
</head>
<body>
    このページに記載予定の内容は以下の通りです。<br>
    ・プロフィール投稿<br>
    ・質問集への回答投稿<br>
    </body>
</html>

