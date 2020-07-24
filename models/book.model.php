<?php
namespace BookModel;
use Database\Database as Database;  
use PDOException;

class BookModel
{
    public static function getFullBooks($query, $page, $itemperpage)
    {
        try {
            if (!$query || $query == "") {
                $query = '%';
            };
            //pagination
            $begin = ($page - 1) * $itemperpage;
            $sqlfull = "select * from books where LOWER(bookname) like LOWER(:query)";
            $queryFull = Database::queryResults($sqlfull, array(
            'query' => "%" . $query . "%"
            ));
            $rowcount = count($queryFull);

            $sql = "select * from books where LOWER(bookname) like LOWER(:query) order by releasedate desc limit $begin, $itemperpage";
            $result = Database::queryResults($sql, array(
            'query' => "%" . $query . "%",
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
    public static function getBookDetail($bookid){
        try {
            $sql = "select b.bookid, b.bookname, b.bookimageurl, b.bookdescription, b.bookpages, b.bookweight, b.	releasedate, a.authorname, c.categoryname, b.price from books as b, authors as a, categories as c where b.bookid = ? and c.categoryid = b.categoryid and a.authorid = b.authorid ";
            $result = Database::querySingleResult($sql,  array($bookid));
            return $result;
        } catch (PDOException $e) {
        return false;
        }
    }
}
?>