<?php
$host = "localhost";
$dbname = "BookSeller";
$user="root";
$pass="";

try {
  $conn = new PDO("mysql:host=".$host.";dbname=".$dbname.";charset=utf8", $user, $pass);
}
catch (PDOException $e) {
  die("Không thể kết nối:  ".$e->getMessage());
}
?>