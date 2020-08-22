<?php
    namespace HomePage;

    use Database\Database as Database;  
    use Status\Status as Status;
    use PDOException;

    class HomePage
    {
        public static function getShowBooks($page, $itemperpage)
        {
            try {
                //pagination
                $begin = ($page - 1) * $itemperpage;
                $sqlfull = "select * from books";
                $queryFull = Database::queryResults($sqlfull, array());
                $rowcount = count($queryFull);
                //show books each page
                $sql = "select * from books order by releasedate desc limit $begin, $itemperpage";
                $result = Database::queryResults($sql, array());
                return [
                'result' => $result,
                'rowcount' => $rowcount
                ];
            } catch (PDOException $e) {
                print_r($e->getMessage());
                return false;
            }
        }
        public static function getBooks()
        {
            try {
                $sql = "select bookid, bookname ,bookimageurl, price from books ORDER BY releasedate DESC limit 1,12";
                $result = Database::queryResults($sql, array());
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }

        public static function getCategory(){
            try {
                $sql = "select categoryid, categoryname from categories order by categoryid ASC limit 0,7";
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
        public static function getBanner() {
            try {
                $sql = "select bookid, customimage from banner";
                $result = Database::queryResults($sql, array());
                return $result;
            } catch (PDOException $e) {
                return false;
            }
        }
    }
?>