<?php

// Origin null is not allowed by Access-Control-Allow-Origin.とかのエラー回避の為、ヘッダー付与
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");


include("functions.php");

$pdo = connect_to_db();

// POSTされたJSON文字列を取り出し
$json = file_get_contents("php://input");

// JSON文字列をobjectに変換
//   ⇒ 第2引数をtrueにしないとハマるので注意
$contents = json_decode($json, true);

if (
  !isset($contents["user_address"]) || $contents["user_address"] == '' ||
  !isset($contents["rps"]) || $contents["rps"] == ''
) {
  echo json_encode(["error_msg" => "no input"]);
  exit();
}

$user_address = $contents["user_address"];
$rps = $contents["rps"];
// var_dump($user_address);
// var_dump($rps);
// exit();


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

// var_dump($result);

// DBにデータがなければデータを新規作成、データがあればそのデータを更新
if ($result == false) {
  $sql = "INSERT INTO rps_table(id, user_address, rps, created_at, updated_at) VALUES (null,:user_address,0,sysdate(),sysdate())";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_address', $user_address, PDO::PARAM_STR);
  $status = $stmt->execute();

if ($status == false) {
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg　データ新規作成エラー" => "{$error[2]}"]);
  exit();
} else {
  // echo "データ新規作成成功";
  exit();
}


} else{
  $sql = "UPDATE rps_table SET rps= rps + :rps, updated_at=sysdate() WHERE user_address=:user_address";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':rps', $rps, PDO::PARAM_STR);
  $stmt->bindValue(':user_address', $user_address, PDO::PARAM_STR);
  $status = $stmt->execute();

if ($status == false) {
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg　データ更新エラー" => "{$error[2]}"]);
  exit();
} else {
  // echo "データ更新成功";
  exit();
}

}
