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
  public static function addAuthors($authorname, $authordescription)
  {
    $sql = "insert into authors (authorname,authordescription) value (?,?)";
    try {
      Database::queryExecute($sql, array($authorname, $authordescription));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public static function editAuthors($id, $name, $description)
  {
    $sql = "update authors set authorname=?,authordescription=? where authorid=?";
    try {
      Database::queryExecute($sql, array($name, $description, $id));
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
}
