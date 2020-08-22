<?php

namespace CategoryModel;

use Database\Database as Database;
use PDOException;

class CategoryModel
{
    public static function getCategories()
    {
        try {
            $sql = "select categoryid, categoryname from categories";
            $result = Database::queryResults($sql, array());
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function getLimitedCategory($limit = 7)
    {
        try {
            $sql = "select categoryid, categoryname from categories order by categoryid ASC limit 0," . $limit;
            $result = Database::queryResults($sql, array());
            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }
    public static function getSingleCategory($categoryid)
    {
        $sql = "select categoryname, categoryid from categories where categoryid=" . $categoryid;
        $result = Database::querySingleResult($sql, array());
        return $result;
    }

    public static function addCategories($categoryname)
    {
        $sql = "insert into categories (categoryname) value (?)";
        try {
            Database::queryExecute($sql, array($categoryname));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function editCategories($categoryid, $categoryname)
    {
        $sql = "update categories set categoryname=? where categoryid=?";
        try {
            Database::queryExecute($sql, array($categoryname, $categoryid));
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public static function removeCategories($categoryid)
    {
        $sql = "delete from categories where categoryid=?";
        try {
            $result = Database::queryExecute($sql, array($categoryid));
            return !!$result;
        } catch (PDOException $e) {
            return false;
        }
    }
}
