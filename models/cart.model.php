<?php

namespace CartModel;

use Database\Database as Database;
use PDO;
use PDOException;
use Symfony\Component\VarDumper\Cloner\Data;

class CartModel
{
  public static function getBook($bookid)
  {
    $sql = "
    select bookid, bookname, bookimageurl, price, authorname, `authors`.authorid
    from books, `authors`
    where books.authorid=`authors`.authorid
    and bookid = ?
    ";
    $result = Database::querySingleResult($sql, array($bookid));
    return $result;
  }
  public static function getItemsDetail($cart)
  {
    try {
      $totalMoney = 0;
      $books = [];
      foreach ($cart as $bookid => $quantity) {
        $book = self::getBook($bookid);
        $book["quantity"] = $quantity;
        $book["amount"] = $book["quantity"] * $book["price"];
        $totalMoney += $book["amount"];
        // $book["price"] = number_format($book["price"], 0, ",", ".");
        array_push($books, $book);
      }
      // $totalMoney = number_format($totalMoney, 0, ",", ".");
      return [
        'totalmoney' => $totalMoney,
        'books' => $books
      ];
    } catch (PDOException  $e) {
      return false;
    }
  }

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

  public static function savePurchasedOrder($userid,  $receivername,  $addressid,  $totalmoney,  $phone,  $shippingid,  $paymentid,  $addresstext)
  {
    try {
      if ($userid) {
        $sql = "insert into orders (userid, orderstatus, timestamp, addressid, addresstext, totalmoney, receivername, phone, shipping, payment)
      value (?, 'p', current_timestamp(), ?, ?, ?, ?, ?,?,?);
      ";
        $result = Database::queryExecute($sql, array($userid, $addressid, $addresstext, $totalmoney, $receivername, $phone, $shippingid, $paymentid));
      } else {
        $sql = "insert into orders (orderstatus, timestamp, addressid, addresstext, totalmoney, receivername, phone, shipping, payment)
        value ('p', current_timestamp(), ?, ?, ?, ?, ?,?,?);
        ";
        $result = Database::queryExecute($sql, array($addressid, $addresstext, $totalmoney, $receivername, $phone, $shippingid, $paymentid));
      }

      if ($result) {
        $sqlgetid = Database::getLastInsertId();
        return $sqlgetid;
      }
      return false;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function savePurchasedOrderDetail($orderid, $cart) 
  {
    try {
      $sql = "insert into ordersdetails (orderid, bookid, qtyordered , amount)
      value (?,?,?,?);
      ";
      foreach ($cart['books'] as $index => $value) {
        $bookid = $value['bookid'];
        $amount = $value['amount'];
        $qtyordered = $value['quantity'];
        $result = Database::queryExecute($sql, array($orderid, $bookid, $qtyordered, $amount));
      }
      return true;
    }
    catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
}
