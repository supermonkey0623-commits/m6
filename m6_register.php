<!DOCTYPE html> 
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ミッション6</title>
</head>
<body>
     ユーザ登録ページ<br>
    こちらでユーザ登録を行ってください。<br>
    すでに登録済みの方は<a href="m6_login.php">ログインページ</a>へ<br><br>
    <!-- ユーザ登録フォーム -->
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" required>
        <input type="email" name="email" placeholder="メールアドレス" required>
        <input type="password" name="password" placeholder="パスワード" required>
        <input type="submit" name="register" value="登録">
    </form>
    </body>
</html>

    <?php
    
    session_start();

// DB 接続設定
$dsn = 'mysql:dbname=データベース名;host=localhost';    
$user = 'ユーザ名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));       
if (!empty($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if($name!=="" && $email!=="" && $password!==""){
    
    // パスワードをハッシュ化
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ユーザ情報をデータベースに挿入
    try{
    $sql = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)");
    $sql->bindParam(':name', $name, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->bindParam(':password_hash', $password_hash, PDO::PARAM_STR);
    $sql->execute();

    echo "ユーザ登録が完了しました。";
    echo "<br>登録したメールアドレスとパスワードで<a href='m6_login.php'>ログインページ</a>へ";
}catch (PDOException $e) {
    if ($e->getCode() == 23000) { // 重複エラーコード
        echo "このメールアドレスは既に登録されています。";
    } else {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}
}
}
?>
