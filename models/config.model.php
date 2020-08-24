<?php

namespace Config;

use Database\Database as Database;
use PDOException;
use Symfony\Component\VarDumper\Cloner\Data;

class ConfigModel

{
  public static function getFooterConfig() {
    try {
      $sql = "select config_content from config where config_name = 'footer'";
      $result = Database::querySingleResult($sql,[]);
      if ($result) {
        return $result['config_content'];
      }
      else return "";
    }
    catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }

  public static function setFooterConfig($content) {
    try {
      $sql = "update config set config_content=? where config_name = 'footer'";
      $result = Database::queryExecute($sql,[$content]);
      if ($result) {
        return $result;
      }
      else return "";
    }
    catch (PDOException $e) {
      print_r($e->getMessage());
      return false;
    }
  }
}