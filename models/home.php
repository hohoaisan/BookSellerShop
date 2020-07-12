<?php
    namespace HomePage;

    use Database\Database as Database;  
    use PDOException;

    class HomePage
    {
        public static function getBooks()
        {
            try {
                $sql = "select bookid, bookname ,bookimageurl, price from books";
                $result = Database::queryResults($sql, array());
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }

        public static function getCategory(){
            try {
                $sql = "select categoryid, categoryname from categories";
                $result = Database::queryResults($sql, array());
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }
        public static function mostSeller(){
            try {
                $sql = "select * from books ORDER BY purchasedcount DESC limit 1,18";
                $result = Database::queryResults($sql, array());
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }
        public static function mostPopular(){
            try {
                $sql = "select * from books ORDER BY viewcount DESC limit 1,18";
                $result = Database::queryResults($sql, array());
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }
    }
?>