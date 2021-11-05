<?php

include("functions.php");

$pdo = connect_to_db();

$user_address = $_GET["user_address"];


// DBからアドレスが一致するデータを取得
$sql = "SELECT * FROM rps_table WHERE user_address=:user_address";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_address', $user_address, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status == false) {
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Origin null is not allowed by Access-Control-Allow-Origin.とかのエラー回避の為、ヘッダー付与
header("Access-Control-Allow-Origin: *");

// JSONを返す
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);