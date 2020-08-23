<?php

namespace OrderModel;

use Database\Database as Database;
use PDOException;

class OrderModel
{
  public static function getOrders($filter, $query, $page, $itemperpage)
  {
    try {

      if (!$query || $query == "") {
        $query = '%';
      };


      //pagination
      $begin = ($page - 1) * $itemperpage;
      $sqlfull = "
        select
        orderid, orders.userid,
        (select users.fullname from users WHERE users.userid=orders.userid) as fullname,
        orderstatus, receivername
        from orders
        having orderstatus like :filter
        and (orderid like :query
            or LOWER(receivername) like LOWER(:query)
            or LOWER(fullname) like LOWER(:query))";
      $queryFull = Database::queryResults($sqlfull, array(
        ':filter' => $filter,
        'query' => "%" . $query . "%",
      ));
      $rowcount = count($queryFull);


      $sql = "
      select
      orderid, orders.userid,
      (select users.fullname from users WHERE users.userid=orders.userid) as fullname,
      receivername, orderstatus, timestamp,totalmoney
      from orders
      having orderstatus like :filter
      and (orderid like :query
        or LOWER(receivername) like LOWER(:query)
        or LOWER(fullname) like LOWER(:query))
      order by timestamp desc
      limit $begin, $itemperpage";
      $result = Database::queryResults($sql, array(
        ':filter' => $filter,
        'query' => "%" . $query . "%",
      ));
      return [
        'result' => $result,
        'rowcount' => $rowcount
      ];
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }

  public static function getOrder($orderid)
  {
    try {
      $sql = "
      SELECT
	orderid,
	orders.userid,
	( SELECT users.fullname FROM users WHERE users.userid = orders.userid ) AS fullname,
	receivername,
	orderstatus,
	TIMESTAMP,
	concat_ws(
          ', ',
          orders.addresstext,(
          SELECT
            CONCAT_WS( ', ', ward.`name`, district.`name`, province.`name` ) AS address 
          FROM
            ward,
            district,
            province 
          WHERE
            ward.did = district.id 
            AND district.pid = province.id 
            AND ward.id = orders.addressid 
          )) AS address,
        concat_ws('', (select payment.`name` from payment where payment.id = orders.payment)) as `payment`,
        concat_ws('', (select shipping.`name` from shipping where shipping.id = orders.shipping)) as `shipping`,
        totalmoney,
        phone 
      FROM
        orders
      
      ";
      $result = Database::querySingleResult($sql, array($orderid));
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function getOrderDetail($orderid)
  {
    try {
      $sql = "SELECT books.bookid, books.bookname, qtyordered, amount from ordersdetails, books where books.bookid = ordersdetails.bookid and ordersdetails.orderid=?";
      $result = Database::queryResults($sql, array($orderid));
      return $result;
    } catch (PDOException $e) {
    }
  }

  public static function getOrdersByUserId($userid, $currentPage, $itemperpage)
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
  public static function rejectOrder($orderid)
  {
    try {
      $sql = "update orders set orderstatus='r' where orderid = ?";
      $result = Database::queryExecute($sql, array($orderid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function acceptOrder($orderid)
  {
    try {
      $sql = "update orders set orderstatus='a' where orderid = ?";
      $result = Database::queryExecute($sql, array($orderid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function completeOrder($orderid)
  {
    try {
      $sql = "update orders set orderstatus='c' where orderid = ?";
      $result = Database::queryExecute($sql, array($orderid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function makeOrderError($orderid)
  {
    try {
      $sql = "update orders set orderstatus='e' where orderid = ?";
      $result = Database::queryExecute($sql, array($orderid));
      return !!$result;
    } catch (PDOException $e) {
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
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }


  public static function checkUserHasBoughtBook($userid, $bookid)
  {
    $sql = "
    select count(ordersdetails.bookid) as COUNT
from ordersdetails, orders
WHERE ordersdetails.orderid = orders.orderid
and orders.userid = ?
and ordersdetails.bookid = ?";
    try {
      $result = Database::querySingleResult($sql, array($userid, $bookid));
      return $result['COUNT'] > 0;
    } catch (PDOException $e) {
      print_r($e->getMessage());
    }
  }
}
