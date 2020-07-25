<?php
    namespace AuthorModel;
    use Database\Database as Database;  
    use PDOException;
    
    class AuthorModel
    {
        public static function getAllAuthor($page, $itemperpage){
            try {
                //pagination
                $begin = ($page - 1) * $itemperpage;
                $sqlfull = "select authorid, authorname, authordescription from authors";
                $queryFull = Database::queryResults($sqlfull, array());
                $rowcount = count($queryFull);
    
                $sql = "select authorid, authorname, authordescription from authors limit $begin, $itemperpage";
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

        public static function getSingleAuthor($authorid){
            $sql = "select authorid, authorname, authordescription from authors where authorid=".$authorid;
            $result = Database::querySingleResult($sql, array());
            return $result;
        }

        public static function getAuthorBooks($authorid, $page, $itemperpage)
        {   
        try {
            //pagination
            $begin = ($page - 1) * $itemperpage;
            $sqlfull = "select * from books where authorid like :authorid";
            $queryFull = Database::queryResults($sqlfull, array(
            'authorid' => "%" . $authorid . "%"
            ));
            $rowcount = count($queryFull);

            $sql = "select * from books where authorid like :authorid limit $begin, $itemperpage";
            $result = Database::queryResults($sql, array(
            'authorid' => "%" . $authorid . "%",
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