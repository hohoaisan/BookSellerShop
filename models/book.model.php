<?php
namespace BookModel;
use Database\Database as Database;  
use PDOException;

class BookModel
{
    public static function getBook($id){
        try {
            $sql = "select * from `books` where bookid = ?";
            $result = Database::querySingleResult($sql, $id);
            return $result;
        } catch (PDOException $e) {
        return false;
        }
    }

    public static function getCategory($id){
        try {
            $sql = "select * from categories where categoryid = ?";
            $result = Database::querySingleResult($sql, $id);
            return $result;
        } catch (PDOException $e) {
        return false;
        }
    }

    public static function getAuthor($id){
        try {
            $sql = "select * from `authors` where authorid = ?";
            $result = Database::querySingleResult($sql, $id);
            return $result;
        } catch (PDOException $e) {
        return false;
        }
    }
}
?>