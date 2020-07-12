<?php

namespace AdminModel;

use Database\Database as Database;
use PDOException;

class AdminModel
{
  public static function getAuthors()
  {
    try {
      $sql = "select authorid, authorname, authordescription from `authors`";
      $result = Database::queryResults($sql, array());
      return $result;
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

  public static function getBooks() {
    try {
      $sql = "select bookid, bookname, authorname, categoryname, `timestamp`, purchasedcount, viewcount, quantity, books.authorid, books.categoryid, price
      from books,`authors`,categories
      WHERE books.authorid = `authors`.authorid
      and books.categoryid = categories.categoryid
      order by bookid asc
      ";
      $result = Database::queryResults($sql,array());
      return $result;
    }
    catch (PDOException $e) {
      return false;
    }

  }

  public static function addBook($bookname,$bookdescription,$bookpages,$bookweight,$releasedate,$authorid,$categoryid,$price, $quantity, $bookimageurl) {
    try {
      $sql = "insert into books (bookname,bookdescription,bookpages,bookweight,releasedate,authorid,categoryid, price,quantity,bookimageurl, timestamp) values (?, ?, ?, ? , ?, ? ,?, ?, ?, ?, CURRENT_TIME())";
      return Database::queryExecute($sql, array($bookname,$bookdescription,$bookpages,$bookweight,$releasedate,$authorid,$categoryid, $price, $quantity, $bookimageurl));
    }
    catch (PDOException $e) {
      echo $e->getMessage();
      return false;
    }
  }
  public static function getBook($bookid) {
    try {
      $sql = "select bookid, bookname,bookdescription,bookpages,bookweight,releasedate,authorid,categoryid, price,quantity,bookimageurl, timestamp from books where bookid = ?";
      $result = Database::querySingleResult($sql, array($bookid));
      return $result;
    }
    catch (PDOException $e) {
      return false;
    }
  }
  
  public static function editBook($bookid, $bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl) {
    try {
      if ($bookimageurl) {
        $sql = "update books 
      set bookname=?, bookdescription=?, bookpages=?, bookweight=?, releasedate=?, authorid=?, categoryid=?, price=?, quantity=?, bookimageurl=?
      where bookid = ?";
      $result = Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl, $bookid));
      return $result;
    }
    else {
      $sql = "update books 
    set bookname=?, bookdescription=?, bookpages=?, bookweight=?, releasedate=?, authorid=?, categoryid=?, price=?, quantity=?
    where bookid = ?";
    $result = Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookid));
      return $result;
      }
    }
    catch (PDOException $e) {
      return false;
    }
  }

  public static function removeBook($bookid) {
    $sql = "delete from books where bookid=?";
    try {
      $result = Database::queryExecute($sql, array($bookid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
  public static function editBookSimple($bookid,$quantity, $price) {
    $sql = "update books set quantity=?, price=? where bookid=?";
    try {
      $result = Database::queryExecute($sql, array($quantity,$price, $bookid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }
}


