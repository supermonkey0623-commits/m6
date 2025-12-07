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
  <title>ミッション6</title>
</head>
<body>
    このページに記載予定の内容は以下の通りです。<br>
    写真しりとりゲーム：<br>
    ・最初の人が「お気に入りのもの」に関する写真をアップロード<br>
    ・次の人はその写真の中にあるものの名前の最後の文字から始まる写真をアップロード<br>
    ・以降、同様にしりとりを続ける<br>
    ・制限時間は5分間<br>


    </body>
</html>
   