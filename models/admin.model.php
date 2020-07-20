<?php

namespace AdminModel;

use Database\Database as Database;
use PDO;
use PDOException;

class AdminModel
{
  public static function getAllAuthors()
  {
    try {
      $sql = "select authorid, authorname, authordescription from `authors`";
      $result = Database::queryResults($sql, array());
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function getAuthors($query, $page, $itemperpage)
  {
    try {
      $begin = ($page - 1) * $itemperpage;
      $sqlCount = "select authorid, authorname, authordescription from `authors`
      where authorid like :query or authorname like :query or authordescription like :query";

      $sql = "select authorid, authorname, authordescription from `authors`
      where authorid like :query or authorname like :query or authordescription like :query
      limit $begin, $itemperpage
      ";

      $queryFull = Database::queryResults($sqlCount, array(
        ':query' => $query ? "%" . $query . "%" : "%"
      ));
      $rowcount = count($queryFull);
      $result = Database::queryResults($sql, array(
        ':query' => $query ? "%" . $query . "%" : "%", //Nếu không có từ khoá thì đặt là %
      ));
      return [
        'result' => $result,
        'rowcount' => $rowcount
      ];
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function addAuthor($authorname, $authordescription)
  {
    $sql = "insert into authors (authorname,authordescription) value (?,?)";
    try {
      Database::queryExecute($sql, array($authorname, $authordescription));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editAuthor($id, $name, $description)
  {
    $sql = "update authors set authorname=?,authordescription=? where authorid=?";
    try {
      Database::queryExecute($sql, array($name, $description, $id));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function removeAuthor($id)
  {
    $sql = "delete from authors where authorid=?";
    try {
      $result = Database::queryExecute($sql, array($id));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }

  //for categories
  public static function getCategories()
  {
    try {
      $sql = "select categoryid, categoryname from categories";
      $result = Database::queryResults($sql, array());
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function addCategories($categoryname)
  {
    $sql = "insert into categories (categoryname) value (?)";
    try {
      Database::queryExecute($sql, array($categoryname));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editCategories($categoryid, $categoryname)
  {
    $sql = "update categories set categoryname=? where categoryid=?";
    try {
      Database::queryExecute($sql, array($categoryname, $categoryid));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function removeCategories($categoryid)
  {
    $sql = "delete from categories where categoryid=?";
    try {
      $result = Database::queryExecute($sql, array($categoryid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function getBooks($query, $authorid, $page, $itemperpage)
  {
    try {

      $begin = ($page - 1) * $itemperpage;
      $sqlCount = "select bookid, bookname, authorname, books.authorid, books.categoryid
      from books,`authors`,categories
      WHERE books.authorid = `authors`.authorid
      and books.categoryid = categories.categoryid
      having (bookid like :query or bookname like :query) and (books.authorid like :authorid)
      ";

      $sql = "select bookid, bookname, authorname, categoryname, `timestamp`, purchasedcount, viewcount, quantity, books.authorid, books.categoryid, price
      from books,`authors`,categories
      WHERE books.authorid = `authors`.authorid
      and books.categoryid = categories.categoryid
      having (bookid like :query or bookname like :query) and (books.authorid like :authorid)
      order by bookid asc
      limit $begin, $itemperpage
      ";

      $queryFull = Database::queryResults($sqlCount, array(
        ':query' => $query ? "%" . $query . "%" : "%", //Nếu không có từ khoá thì đặt là %
        ':authorid' => $authorid ? "%" . $authorid . "%" : "%",
      ));
      $rowcount = count($queryFull);

      $result = Database::queryResults($sql, array(
        ':query' => $query ? "%" . $query . "%" : "%", //Nếu không có từ khoá thì đặt là %
        ':authorid' => $authorid ? "%" . $authorid . "%" : "%",
      ));

      return [
        'result' => $result,
        'rowcount' => $rowcount
      ];
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function addBook($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $price, $quantity, $bookimageurl)
  {
    try {
      $sql = "insert into books (bookname,bookdescription,bookpages,bookweight,releasedate,authorid,categoryid, price,quantity,bookimageurl, timestamp) values (?, ?, ?, ? , ?, ? ,?, ?, ?, ?, CURRENT_TIME())";
      return Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $price, $quantity, $bookimageurl));
    } catch (PDOException $e) {
      echo $e->getMessage();
      return false;
    }
  }
  public static function getBook($bookid)
  {
    try {
      $sql = "select bookid, bookname,bookdescription,bookpages,bookweight,releasedate,authorid,categoryid, price,quantity,bookimageurl, timestamp from books where bookid = ?";
      $result = Database::querySingleResult($sql, array($bookid));
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editBook($bookid, $bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl)
  {
    try {
      if ($bookimageurl) {
        $sql = "update books 
      set bookname=?, bookdescription=?, bookpages=?, bookweight=?, releasedate=?, authorid=?, categoryid=?, price=?, quantity=?, bookimageurl=?
      where bookid = ?";
        $result = Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl, $bookid));
        return $result;
      } else {
        $sql = "update books 
    set bookname=?, bookdescription=?, bookpages=?, bookweight=?, releasedate=?, authorid=?, categoryid=?, price=?, quantity=?
    where bookid = ?";
        $result = Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookid));
        return $result;
      }
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function removeBook($bookid)
  {
    $sql = "delete from books where bookid=?";
    try {
      $result = Database::queryExecute($sql, array($bookid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function editBookSimple($bookid, $quantity, $price)
  {
    $sql = "update books set quantity=?, price=? where bookid=?";
    try {
      $result = Database::queryExecute($sql, array($quantity, $price, $bookid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }


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
          having userid like :query or username like :query or fullname like :query or phone like :query
          ";
          $sql = "
          select userid, username, fullname,dob, phone, email, male, addressid, addresstext, isdisable, isadmin
          from users
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
      $sql = "select orderid, orders.userid, (select users.fullname from users WHERE users.userid=orders.userid) as fullname, receivername, orderstatus, timestamp, concat_ws(', ',orders.addresstext,(select CONCAT_WS(', ', ward.`name`,district.`name`, province.`name`) as address FROM ward,district, province WHERE ward.did=district.id and district.pid=province.id and ward.id=orders.addressid)) as address,totalmoney, phone from orders where orderid=?";
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
}
