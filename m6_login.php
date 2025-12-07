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
     ログインページ<br>
    こちらでログインを行ってください。<br>
    会員登録がまだの方は<a href="m6_register.php">会員登録ページ</a>へ<br><br>
    <!-- ログインフォーム -->
    <form action="" method="post">
        <input type="email" name="email" placeholder="メールアドレス" required>
        <input type="password" name="password" placeholder="パスワード" required>
        <input type="submit" name="login" value="ログイン">
    </form>
 <?php
// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';    
$user = 'ユーザ名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));   

if (!empty($_POST['login'])) {
    $email = trim($_POST['email']);
    $pw = $_POST['password'];

    // 入力チェック
    if ($email !== "" && $pw !== "") {
        $sql = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $sql->bindParam(':email', $email, PDO::PARAM_STR);
        $sql->execute();
        $user = $sql->fetch();

        if ($user && password_verify($pw, $user['password_hash'])) {
            // ログイン成功
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            echo "ログイン成功！ようこそ " . htmlspecialchars($user['name'], ENT_QUOTES) . " さん。";
            header("Location: m6_rooms.php");
            exit;
    
        } else {
            echo "メールアドレスまたはパスワードが違います。";
        }
    } else {
        echo "入力に不備があります。";
    }
}


?>
</body>
</html>