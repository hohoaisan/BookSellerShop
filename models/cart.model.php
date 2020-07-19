<?php

namespace CartModel;

use Database\Database as Database;
use PDO;
use PDOException;

class CartModel {
  public static function getBook($bookid) {
    $sql = "
    select bookid, bookname, bookimageurl, price, authorname, `authors`.authorid
    from books, `authors`
    where books.authorid=`authors`.authorid
    and bookid = ?
    ";
    $result = Database::querySingleResult($sql, array($bookid));
    return $result;
  }
  public static function getItemsDetail($cart) {
    try {
      $totalMoney = 0;
      $books = [];
      foreach ($cart as $bookid => $quantity) {
        $book = self::getBook($bookid);
        $book["quantity"] = $quantity;
        $book["amount"] = $book["quantity"]*$book["price"];
        $totalMoney+=$book["amount"];
        array_push($books, $book);
      }
      return [
        'totalmoney' => $totalMoney,
        'books' => $books
      ];
    }
    catch (PDOException  $e) {
      return false;
    }
    
  }
}