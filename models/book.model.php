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
    public static function getBooks($query, $authorid, $page, $itemperpage)
    {
        try {

            $begin = ($page - 1) * $itemperpage;
            $sqlCount = "select bookid, bookname, authorname, books.authorid, books.categoryid
      from books,`authors`,categories
      WHERE books.authorid = `authors`.authorid
      and books.categoryid = categories.categoryid
      having (bookid like :query or bookname like :query) and (books.authorid like :authorid)
      ";

            $sql = "select bookid, bookname, authorname, categoryname, `timestamp`, purchasedcount, viewcount, quantity, books.authorid, books.categoryid, price
      from books,`authors`,categories
      WHERE books.authorid = `authors`.authorid
      and books.categoryid = categories.categoryid
      having (bookid like :query or bookname like :query) and (books.authorid like :authorid)
      order by bookid asc
      limit $begin, $itemperpage
      ";

            $queryFull = Database::queryResults($sqlCount, array(
                ':query' => $query ? "%" . $query . "%" : "%", //Nếu không có từ khoá thì đặt là %
                ':authorid' => $authorid ? "%" . $authorid . "%" : "%",
            ));
            $rowcount = count($queryFull);

            $result = Database::queryResults($sql, array(
                ':query' => $query ? "%" . $query . "%" : "%", //Nếu không có từ khoá thì đặt là %
                ':authorid' => $authorid ? "%" . $authorid . "%" : "%",
            ));

            return [
                'result' => $result,
                'rowcount' => $rowcount
            ];
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getLastestBooks($page, $itemperpage)
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
    public static function getBookDetail($bookid)
    {
        try {
            $sql = "select b.bookid, b.bookname, b.bookimageurl, b.bookdescription, b.bookpages, b.bookweight, b.	releasedate, a.authorname, c.categoryname, b.price, publisher, bookbinding, quantity
            from books as b, authors as a, categories as c
            where b.bookid = ? and c.categoryid = b.categoryid and a.authorid = b.authorid ";
            $sqlViewBooks = "update books set viewCount = ? where bookid = ?";
            $sqlBooks = "select viewcount from books where bookid = ?";
            $viewCount = Database::querySingleResult($sqlBooks, array($bookid));
            $viewCount["viewcount"] += 1;
            Database::queryExecute($sqlViewBooks, array($viewCount["viewcount"], $bookid));
            $result = Database::querySingleResult($sql,  array($bookid));
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getBookRelated($bookid)
    {
        try {
            $sql = "
            
                select bookid, bookname ,bookimageurl, price, categoryid, quantity, authorid
                from
                (
                    select bookid, bookname ,bookimageurl, price, categoryid, quantity, books.authorid
                    from books,`authors`
                    where books.authorid = `authors`.authorid
                    and `authors`.authorid = (select authorid from books WHERE bookid=?)
                    union	
                    select bookid, bookname ,bookimageurl, price, books.categoryid, quantity, authorid
                    from books,`categories`
                    where books.categoryid = `categories`.categoryid
                    and categories.categoryid = (select categoryid from books WHERE bookid=?)
                ) as result

                limit 12

            ";
            $result = Database::queryResults($sql,  array($bookid, $bookid));
            return $result;
        } catch (PDOException $e) {
            print_r($e->getMessage());
            return false;
        }
    }



    public static function addBook($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $price, $quantity, $bookimageurl, $publisher, $bookbind)
    {
        try {
            $sql = "insert into books (bookname,bookdescription,bookpages,bookweight,releasedate,authorid,categoryid, price,quantity,bookimageurl, timestamp, publisher, bookbinding) values (?, ?, ?, ? , ?, ? ,?, ?, ?, ?, CURRENT_TIME(), ? ,? )";
            return Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $price, $quantity, $bookimageurl, $publisher, $bookbind));
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
    public static function getBook($bookid)
    {
        try {
            $sql = "select bookid, bookname,bookdescription,bookpages,bookweight,releasedate,authorname, `authors`.authorid,categoryid, price,quantity,bookimageurl, timestamp, publisher, bookbinding
            from books, `authors`
            where books.authorid=`authors`.authorid
            and bookid = ?";
            $result = Database::querySingleResult($sql, array($bookid));
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function editBook($bookid, $bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl, $publisher, $bookbind)
    {
        try {
            if ($bookimageurl) {
                $sql = "update books 
      set bookname=?, bookdescription=?, bookpages=?, bookweight=?, releasedate=?, authorid=?, categoryid=?, price=?, quantity=?, bookimageurl=?, publisher=?, bookbinding=?
      where bookid = ?";
                $result = Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl, $publisher, $bookbind, $bookid));
                return $result;
            } else {
                $sql = "update books 
    set bookname=?, bookdescription=?, bookpages=?, bookweight=?, releasedate=?, authorid=?, categoryid=?, price=?, quantity=?,  publisher=?, bookbinding=?
    where bookid = ?";
                $result = Database::queryExecute($sql, array($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $publisher, $bookbind, $bookid));
                return $result;
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function removeBook($bookid)
    {
        $sql = "delete from books where bookid=?";
        try {
            $result = Database::queryExecute($sql, array($bookid));
            return !!$result;
        } catch (PDOException $e) {
            return false;
        }
    }
    public static function editBookSimple($bookid, $quantity, $price)
    {
        $sql = "update books set quantity=?, price=? where bookid=?";
        try {
            $result = Database::queryExecute($sql, array($quantity, $price, $bookid));
            return !!$result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getMostSeller(){
        try {
            $sql = "select * from books ORDER BY purchasedcount DESC limit 1,18";
            $result = Database::queryResults($sql, array());
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }
    public static function getMostPopular(){
        try {
            $sql = "select * from books ORDER BY viewcount DESC limit 1,18";
            $result = Database::queryResults($sql, array());
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    
}
