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

  public static function getUserOrders($userid, $currentPage, $itemperpage)
  {
    try {
      $sql = "select orderid, orders.userid, (select users.fullname from users WHERE users.userid=orders.userid) as fullname, orderstatus, timestamp, totalmoney
          from orders WHERE userid=? order by timestamp ASC";
      $queryFull = Database::queryResults($sql, array($userid));
      $rowcount = count($queryFull);

      //pagination
      $begin = ($currentPage - 1) * $itemperpage;
      $sqlPagination = "select orderid, orders.userid, (select users.fullname from users WHERE users.userid=orders.userid) as fullname, orderstatus, timestamp, totalmoney
      from orders WHERE userid=? order by timestamp ASC limit $begin, $itemperpage";
      $result = Database::queryResults($sqlPagination, array($userid));
      return [
        'result' => $result,
        'rowcount' => $rowcount
      ];
    } catch (PDOException $e) {
      return false;
    }
  }
}
