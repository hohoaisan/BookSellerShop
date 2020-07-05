<?php
$host = "localhost";
$dbname = "BookSeller";
$user="root";
$pass="";

try {
  $options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  $conn = new PDO("mysql:host=".$host.";dbname=".$dbname.";charset=utf8", $user, $pass, $options);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}
catch (PDOException $e) {
  die("Không thể kết nối:  ".$e->getMessage());
}


?>