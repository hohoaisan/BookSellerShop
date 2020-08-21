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
	ordersdetails.orderid = orders.orderid 
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
  public static function getRating($ratingid) {
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
  public static function removeRating($ratingid) {
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
}
