<?php

namespace RatingModel;

use Database\Database as Database;
use Status\Status as Status;
use PDOException;

class RatingModel
{
  public static function getPurchasedBooksWithRating($userid)
  {
    $sql = "
    select result.*, rating.ratingid,rating.content, rating.rating, rating.`timestamp`  from 
(
SELECT
	orders.userid,
	ordersdetails.bookid,
	orders.orderid,
	max( orders.`timestamp` ) AS `purchasetime`,
	books.bookname,
  books.price,
	books.bookimageurl,
	`authors`.authorname 
FROM
	ordersdetails,
	orders,
	books,
	`authors` 
WHERE
  orders.orderstatus = 'c'
  AND ordersdetails.orderid = orders.orderid 
	AND orders.userid = :userid 
	AND ordersdetails.bookid IN ( SELECT DISTINCT ordersdetails.bookid FROM ordersdetails, orders WHERE ordersdetails.orderid = orders.orderid AND orders.userid = :userid ) 
	AND ordersdetails.bookid = books.bookid 
	AND `authors`.authorid = books.authorid 
GROUP BY
	ordersdetails.bookid
) as result
left join rating
on rating.bookid = result.bookid
ORDER BY ratingid ,`purchasetime` desc
    ";

    try {
      $result = Database::queryResults($sql, array(':userid' => $userid));
      return $result;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }

  public static function getBookRatings($bookid)
  {
    $sql = "
    select rating.ratingid, rating.bookid ,rating.`timestamp`, rating.rating, rating.content, users.fullname
from rating, users
where rating.userid = users.userid
and bookid = ?";
    try {
      $result = Database::queryResults($sql, array($bookid));
      return $result;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function getRating($ratingid)
  {
    $sql = "select * from rating
    where ratingid = ?";
    try {
      $result = Database::querySingleResult($sql, array($ratingid));
      return $result;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function removeRating($ratingid)
  {
    $sql = "delete from rating
    where ratingid = ?";
    try {
      $result = Database::queryExecute($sql, array($ratingid));
      return $result;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function createRating($userid, $bookid, $rating, $content) {
    $sql = "insert into rating (userid, timestamp, bookid, rating, content)
    values (?,CURRENT_TIMESTAMP(), ?,?,?)
    ";

    try {
      $result = Database::queryExecute($sql, array($userid, $bookid, $rating, $content));
      return $result;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function editRating($ratingid, $rating, $content) {
    $sql = "update rating
    set rating=?,
        content=?,
        timestamp=CURRENT_TIMESTAMP()
    where ratingid=?
    ";

    try {
      $result = Database::queryExecute($sql, array($rating, $content, $ratingid));
      return $result;
    } catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
  public static function checkUserHasRated($userid, $bookid) {
    $sql = "
    select ratingid
from rating
where userid = ?
and bookid = ?";
    try {
      $result = Database::querySingleResult($sql, array($userid, $bookid));
      return isset($result['ratingid']);
    } catch (PDOException $e) {
      print_r($e->getMessage());
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
