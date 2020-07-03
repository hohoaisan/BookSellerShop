<?php

use Pug\Facade as PugFacade;


$postSubadmin = function () {
  if (isset($_POST["key"])) {
    echo "Success post request" . $_POST["key"];
  } else echo "Success Subadmin";
};
$routeparamexample = function ($param1, $param2) {
  echo $param1 . $param2;
};
$getpostdatabaseexample = function () {
  $template = '
a(href=baseurl+"/subadmin?key=value") Catch get parameter

form(action=baseurl+"/subadmin", method="POST")
  input(name="key")
  button Submit

ul
  each row in query
    li= row[0] + row[1]
';
  $host = "localhost";
  $dbname = "test";
  $user = "root";
  $pass = "";

  try {
    $GLOBALS["connection"] = new PDO("mysql:host=" . $host . ";dbname=" . $dbname . ";charset=utf8", $user, $pass);
  } catch (PDOException $e) {
    die("Không thể kết nối:  " . $e->getMessage());
  }

  $sql = "select * from testtable";
  $result = $GLOBALS["connection"]->query($sql);

  echo PugFacade::display($template, ['baseurl' => $_SERVER['REQUEST_URI'], 'query' => $result]);
};
$subadmin = function () {
  if (isset($_GET["key"])) {
    echo "Success get request" . $_GET["key"];
  } else echo "Success Subadmin";
};