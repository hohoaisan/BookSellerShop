<?php

namespace Database;

use Exception;
use PDO;
use PDOException;

class Database
{
  public static function connect()
  {
    $host = "localhost";
    $dbname = "BookSeller";
    $user = "root";
    $pass = "";

    try {
      $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      );
      $conn = new PDO("mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8", $user, $pass, $options);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $conn;
    } catch (PDOException $e) {
      echo "Không thể kết nối:  " . $e->getMessage();
      return false;
    }
  }
  // select from tblX where id=? and usrname=? 
  // $params là 1 array chứa các giá trị tương ứng với số dấu ? trong string sql
  public static function querySingleResult($sql, $params)
  {
    $conn = self::connect();
    $stmt = $conn->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute($params);
    $result = $stmt->fetch();
    $conn = null;
    return $result;
  }
  public static function queryResults($sql, $params)
  {
    $conn = self::connect();
    $stmt = $conn->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    $conn = null;
    return $result;
  }

  //input value vào 
  public static function queryExecute($sql, $params)
  {
    $conn = self::connect();
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute($params);
    $conn = null;
    return $result;
  }

  public static function verifyCredential($username, $password)
  {
    try {
      $sql = "select userid, username, password, isadmin, isdisable from users where username=?";
      $result = self::querySingleResult($sql, array($username));
      if ($result && $result["isdisable"] == 0 && $result["password"] == $password) {
        return [
          'status' => 1, //success
          'userid' => $result["userid"],
          'isadmin' => $result["isadmin"],
        ];
      } else {
        return [
          'status' => 0, //wrong
          'message' => 'Tên tài khoản hoặc mật khẩu không đúng'
        ];
      }
    } catch (PDOException $e) {
      return [
        'status' => -1, //error
        'message' => $e->getMessage()
      ];
    }
  }

}
?>
