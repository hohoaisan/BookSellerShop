<?php namespace Database;

use Exception;
use PDO;
use PDOException;
class Database {
  public static function connect() {
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
    return $conn;
    }
    catch (PDOException $e) {
      echo "Không thể kết nối:  ".$e->getMessage();
      return false;
    }
  }

  public static function verifyCredential($username,$password) {
    try {
      $conn = self::connect();
      $sql = "select userid, username, password, isadmin from users where username=?";
      $stmt = $conn->prepare($sql);
      $stmt->setFetchMode(PDO::FETCH_ASSOC);
      $stmt->execute(array($username));
      $result = $stmt->fetch();
      $conn = null;
      if ($result && $result["password"] == $password) {
        return [
          'status'=> 1, //success
          'userid'=> $result["userid"],
          'isadmin'=> $result["isadmin"],
        ];
      }
      else {
        return [
          'status'=> 0, //wrong
          'message' => 'Tên tài khoản hoặc mật khẩu không đúng'
        ];
      }
    }
    catch (PDOException $e) {
      return [
        'status'=> -1, //error
        'message' => $e->getMessage()
      ];

    }
  }
}
?>