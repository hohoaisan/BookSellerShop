<?php
$index = function() {
  echo "THis is cart router";
};


$router->get('/', $index);
$router->get('/abcxyz', function() {
  echo "funk you";
});
?>