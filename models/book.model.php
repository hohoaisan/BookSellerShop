<?php
namespace BookModel;
use Database\Database as Database;  
use PDOException;

class BookModel
{
    public static function getBook($bookid){
        try {
            $sql = "select b.bookname, b.bookimageurl, b.bookdescription, b.bookpages, b.bookweight, b.	releasedate, a.authorname, c.categoryname, b.price from books as b, authors as a, categories as c where b.bookid = ? and c.categoryid = b.categoryid and a.authorid = b.authorid ";
            $result = Database::querySingleResult($sql,  array($bookid));
            return $result;
        } catch (PDOException $e) {
        return false;
        }
    }
}
?>