<?php

namespace UserModel;

use Database\Database as Database;
use Status\Status as Status;
use PDOException;

class UserModel
{
  public static function getUserInfo($userid)
  {
    try {
      $sql = "select userid,username, fullname, phone, email, male,addressid, addresstext, dob, isadmin
          from users
          WHERE userid=?";
      $result = Database::querySingleResult($sql, array($userid));
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editUserInfo($userid, $fullname, $email, $phone, $male, $dob) {
    try {
      $sql = "update users
      set fullname=?,email=?, phone=?, male=?, dob=?
      where userid=? 
      ";
      $result = Database::queryExecute($sql, array($fullname, $email, $phone, $male, $dob, $userid));
      return $result;
    }

    catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function getUserAddress($userid)
  {
    try {
      $sql = "select userid,username, fullname, phone, email, male,addressid, addresstext, dob
          from users
          WHERE userid=?";
      $result = Database::querySingleResult($sql, array($userid));
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editUserAddress($userid,$addressid, $addresstext)
  {
    try {
      $sql = "update users set addressid=?, addresstext=?
          WHERE userid=?";
      $result = Database::queryExecute($sql, array($addressid, $addresstext, $userid));
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  

  #TODO:Admin

  public static function getUsers($filter, $query, $page, $itemperpage)
  {
    try {
      if (!$query || $query == "") {
        $query = '%';
      };
      $begin = ($page - 1) * $itemperpage;
      switch ($filter) {
        case 'disabled':
          $sqlCount = "
          select userid, username, fullname, phone, isdisable
          from users
          where isdisable=1
          having userid like :query or username like :query or fullname like :query or phone like :query
          ";

          $sql = "
          select userid, username, fullname,dob, phone, email, male, addressid, addresstext, isdisable, isadmin
          from users
          where isdisable=1
          having userid like :query or username like :query or fullname like :query or phone like :query
          order by userid asc
          limit $begin, $itemperpage
          ";
          break;
        case 'admin':
          $sqlCount = "
          select userid, username, fullname, isadmin, phone
          from users
          where isadmin=1
          having userid like :query or username like :query or fullname like :query or phone like :query
          ";

          $sql = "
          select userid, username, fullname,dob, phone, email, male, addressid, addresstext, isdisable, isadmin
          from users
          where isadmin=1
          having userid like :query or username like :query or fullname like :query or phone like :query
          order by userid asc
          limit $begin, $itemperpage
          ";
          break;
        default:
          $sqlCount = "
          select userid, username, fullname, phone
          from users
          where isadmin=0
          having userid like :query or username like :query or fullname like :query or phone like :query
          ";
          $sql = "
          select userid, username, fullname,dob, phone, email, male, addressid, addresstext, isdisable, isadmin
          from users
          where isadmin=0
          having userid like :query or username like :query or fullname like :query or phone like :query
          order by userid asc
          limit $begin, $itemperpage
          ";
      }
      $queryFull = Database::queryResults($sqlCount, array(':query' => "%" . $query . "%",));
      $rowcount = count($queryFull);
      $result = Database::queryResults($sql, array(':query' => "%" . $query . "%",));
      // print_r($result);
      return [
        'result' => $result,
        'rowcount' => $rowcount
      ];
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function removeUser($userid)
  {
    try {
      $sql = "delete from users where userid = ?";
      $result = Database::queryExecute($sql, array($userid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function disableUser($userid)
  {
    try {
      $sql = "update users set isdisable=1 where userid = ?";
      $result = Database::queryExecute($sql, array($userid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function enableUser($userid)
  {
    try {
      $sql = "update users set isdisable=0 where userid = ?";
      $result = Database::queryExecute($sql, array($userid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function removeUserAdmin($userid)
  {
    try {
      $sql = "update users set isadmin=0 where userid = ?";
      $result = Database::queryExecute($sql, array($userid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function makeUserAdmin($userid)
  {
    try {
      $sql = "update users set isadmin=1 where userid = ?";
      $result = Database::queryExecute($sql, array($userid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function getUserJSON($userid)
  {
    try {
      $sql = "select userid, username, fullname,dob, phone, email, male, concat_ws(', ',addresstext,(select CONCAT_WS(', ', ward.`name`,district.`name`, province.`name`) as address FROM ward,district, province WHERE ward.did=district.id and district.pid=province.id and ward.id=addressid)) as address
      from users where userid=?";
      $result = Database::querySingleResult($sql, array($userid));
      $result["male"] = $result["male"] == "1" ? "Nam" : "Nữ";
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  #TODO:Auth
  public static function verifyUser($username, $password)
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
