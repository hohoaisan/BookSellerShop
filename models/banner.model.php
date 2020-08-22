<?php

namespace BannerModel;

use Database\Database as Database;
use PDOException;

class BannerModel
{
  public static function getBanners()
  {
    try {
      $sql = "select bookid, customimage from banner";
      $result = Database::queryResults($sql, array());
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function getBooksForBanner()
  {
    try {
      $sql = "select bookid, bookname from books";
      $result = Database::queryResults($sql, array());
      return $result;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function addBanner($bookid, $customimage)
  {
    $sql = "insert into banner (bookid, customimage) value (?, ?)";
    try {
      Database::queryExecute($sql, array($bookid, $customimage));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function removeBanner($bookid)
  {
    $sql = "delete from banner where bookid=?";
    try {
      $result = Database::queryExecute($sql, array($bookid));
      return !!$result;
    } catch (PDOException $e) {
      return false;
    }
  }

}
