<?php
include_once('../vendor/autoload.php');
include_once('../helpers.php');

use Pug\Facade as PugFacade;
use Pecee\SimpleRouter\SimpleRouter;
// Lấy url của router và xử lí để tránh lỗi, url sẽ được đưa vào các thẻ a, form, ... liên quan đến router hiện tại
$_SERVER['REQUEST_URI'] = "/" . trim($_SERVER['REQUEST_URI'], "/");




//Example database
$host = "localhost";
$dbname = "test";
$user = "root";
$pass = "";

try {
  $GLOBALS["connection"] = new PDO("mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8", $user, $pass);
} catch (PDOException $e) {
  die("Không thể kết nối:  " . $e->getMessage());
}

//Router root /example/
SimpleRouter::get('/', function () {
  $template = '
a(href=baseurl+"/subadmin?key=value") Catch get parameter

form(action=baseurl+"/subadmin", method="POST")
  input(name="key")
  button Submit

ul
  each row in query
    li= row[0] + row[1]
';
  $sql = "select * from testtable";
  $result = $GLOBALS["connection"]->query($sql);

  return PugFacade::display($template, ['baseurl' => $_SERVER['REQUEST_URI'], 'query' => $result]);
});

//Router root /expample/subadmin
SimpleRouter::get('/subadmin', function () {
  if (isset($_GET["key"])) {
    return "Success get request" . $_GET["key"];
  } else return "Success Subadmin";
});

SimpleRouter::post('/subadmin', function () {
  if (isset($_POST["key"])) {
    return "Success post request" . $_POST["key"];
  } else return "Success Subadmin";
});

//Router root /expample/alo/subpath/konichiwa => alokonochiwa
SimpleRouter::get('/{param1}/subpath/{param2}', function ($param1, $param2) {
  return $param1 . $param2;
});
