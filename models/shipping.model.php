<?php

namespace ShippingModel;

use Database\Database as Database;
use PDOException;

class ShippingModel

{
  public static function getDefaultShippingMethod()
  {
    try {
      $sql = "select id, name,pricing,`default`
      from shipping
      where `default`=1
      ";
      $result = Database::querySingleResult($sql, []);
      return $result;
    } catch (PDOException  $e) {
      return false;
    }
  }
  public static function getShippingMethod()
  {
    try {
      $sql = "select id, name,pricing,`default`
      from shipping
      ";
      $result = Database::queryResults($sql, []);
      return $result;
    } catch (PDOException  $e) {
      return false;
    }
  }
}
