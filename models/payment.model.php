<?php

namespace PaymentModel;

use Database\Database as Database;
use PDOException;

class PaymentModel

{
  public static function getDefaultPaymentMethod()
  {
    try {
      $sql = "select id, name,info,`default`
      from payment
      where `default`=1
      ";
      $result = Database::querySingleResult($sql, []);
      return $result;
    } catch (PDOException  $e) {
      return false;
    }
  }
  public static function getPaymentMethod()
  {
    try {
      $sql = "select id, name,info,`default`
      from payment
      ";
      $result = Database::queryResults($sql, []);
      return $result;
    } catch (PDOException  $e) {
      return false;
    }
  }
}
