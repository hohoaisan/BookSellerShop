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
    }
?>