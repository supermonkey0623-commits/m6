<?php
session_start(); // セッションを使えるようにする

// セッション変数をすべて削除
$_SESSION = array();

// セッションを完全に破壊
session_destroy();

// ログインページへリダイレクト
header("Location: m6_login.php");
exit();
?>
