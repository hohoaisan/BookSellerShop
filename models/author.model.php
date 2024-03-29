<?php

namespace AuthorModel;

use Database\Database as Database;
use BookModel\BookModel as BookModel;
use PDOException;

class AuthorModel
{
    public static function getAllAuthors()
    {
        try {
            $sql = "select authorid, authorname, authordescription from `authors`";
            $result = Database::queryResults($sql, array());
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }
    public static function getAuthors($query = null, $page, $itemperpage)
    {
        try {
            $begin = ($page - 1) * $itemperpage;
            $sqlCount = "select authorid, authorname, authordescription from `authors`
                where authorid like :query or authorname like :query or authordescription like :query";

            $sql = "select authorid, authorname, authordescription from `authors`
                where authorid like :query or authorname like :query or authordescription like :query
                limit $begin, $itemperpage
                ";

            $queryFull = Database::queryResults($sqlCount, array(
                ':query' => $query ? "%" . $query . "%" : "%"
            ));
            $rowcount = count($queryFull);
            $result = Database::queryResults($sql, array(
                ':query' => $query ? "%" . $query . "%" : "%", //Nếu không có từ khoá thì đặt là %
            ));
            return [
                'result' => $result,
                'rowcount' => $rowcount
            ];
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getAuthor($authorid)
    {
        $sql = "select authorid, authorname, authordescription from authors where authorid=" . $authorid;
        $result = Database::querySingleResult($sql, array());
        return $result;
    }

    

    public static function addAuthor($authorname, $authordescription)
    {
        $sql = "insert into authors (authorname,authordescription) value (?,?)";
        try {
            Database::queryExecute($sql, array($authorname, $authordescription));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function editAuthor($id, $name, $description)
    {
        $sql = "update authors set authorname=?,authordescription=? where authorid=?";
        try {
            Database::queryExecute($sql, array($name, $description, $id));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function removeAuthor($id)
    {
        $sql = "delete from authors where authorid=?";
        try {
            $result = Database::queryExecute($sql, array($id));
            return !!$result;
        } catch (PDOException $e) {
            return false;
        }
    }
}
