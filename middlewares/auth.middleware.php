<?php
$router->before('GET|POST', '*', function() {
  if (isset($_COOKIE['userid']) && isset($_COOKIE['admin'])) {
    
  }
  else {
    header('location: /auth/login');
  }
});
