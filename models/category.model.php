<?php
    namespace CategoryPage;

    use Database\Database as Database;  
    use Status\Status as Status;
    use PDOException;

    class CategoryPage
    {
        public static function getSingleCategory($categoryid){
            $sql = "select categoryname, categoryid from categories where categoryid=".$categoryid;
            $result = Database::querySingleResult($sql, array());
            return $result;
        }
        public static function getCategoryBooks($categoryid, $page, $itemperpage)
        {
            try {
                //pagination
                $begin = ($page - 1) * $itemperpage;
                $sqlfull = "select * from books where categoryid like :categoryid";
                $queryFull = Database::queryResults($sqlfull, array(
                'categoryid' => "%" . $categoryid . "%"
                ));
                $rowcount = count($queryFull);
    
                $sql = "select * from books where categoryid like :categoryid limit $begin, $itemperpage";
                $result = Database::queryResults($sql, array(
                'categoryid' => "%" . $categoryid . "%",
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
    }
?>