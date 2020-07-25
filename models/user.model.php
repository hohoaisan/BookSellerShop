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
      $sql = "select userid,username, fullname, phone, email, male,addressid, addresstext, dob
          from users
          WHERE userid=?";
      $result = Database::querySingleResult($sql, array($userid));
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editUserInfo()
  {
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

  public static function getUserOrders()
  {
  }
}
