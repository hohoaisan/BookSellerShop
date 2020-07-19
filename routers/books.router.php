<?php
$index = function() {
  echo "THis is book router";
};


$router->get('/{bookid}', $index);
?>