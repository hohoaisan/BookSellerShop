<?php 
namespace AuthModel;
use Database\Database as Database;  
use PDOException;

class AuthModel {
  public static function verifyCredential($username, $password)
  {
    try {
      $password =md5(md5($password));
      $sql = "select userid, username, password, isadmin, isdisable from users where username=?";
      $result = Database::querySingleResult($sql, array($username));
      if ($result && $result["isdisable"] == 0 && $result["password"] == $password) {
        return [
          'status' => 1, //success
          'userid' => $result["userid"],
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
  public static function regiserNewUser($username, $password, $email, $fullname, $male, $phone, $dob) {
    try {
      $password =md5(md5($password));
      $user = [$username, $password, $email, $fullname, $male, $phone, $dob];
      $result = Database::queryExecute("insert INTO users(username, password, email, fullname, male, phone, dob) VALUES (?, ?, ?, ?, ?, ?, ?)", $user);
      return $result;
    }
    catch (PDOException $e) {
      //catch looxi
      return false;
    }

  }
  public static function checkUserExists($username, $email) {
    try {
      $user =[$username, $email];
      $result = Database::querySingleResult("select * from users WHERE username=? OR email=? LIMIT 1",$user);
      return !!$result;
    }
    catch(PDOException $e) {
      return false;
    }

  }


  public static function checkUserId($userid) {
    try {
      $sql = "select userid,isadmin
      from users
      WHERE userid=?";
      $result = Database::querySingleResult($sql, array($userid));
      return $result;
    }
    catch(PDOException $e) {
      return false;
    }
  }
}


?>