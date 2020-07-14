<?php
$index = function() {
  echo "THis is book router";
};


$router->get('/', $index);
$router->get('/abcxyz', function() {
  echo "funk you";
});
?>