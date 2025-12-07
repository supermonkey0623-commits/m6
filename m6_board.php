<?php
session_start();
?>

<!DOCTYPE html> 
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ミッション6</title>
</head>
<body>
    このページに記載予定の内容は以下の通りです。<br>
    <ul>
    <li>投稿ページで記入したメンバーそれぞれのプロフィール</li>
    <li>投稿ページで回答した質問集へのメンバーそれぞれの回答とそれに対するコメント</li>
    </body>
</html>
<?php
  
// ログインしてなければログインページへ強制移動
if (empty($_SESSION['user_id'])) {
    header("Location: m6_login.php");
    exit;
}
?>
