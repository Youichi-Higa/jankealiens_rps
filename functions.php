<?php

function connect_to_db()
{

  // ローカル
  $dbn = 'mysql:dbname=jankealiens;charset=utf8;port=3306;host=localhost';
  $user = 'root';
  $pwd = '';

  try {
    return new PDO($dbn, $user, $pwd);
  } catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
  }
}


