<?php
include_once('vendor/autoload.php');

$pug = new Pug();

$pug->displayFile('views/example.pug', [
  'normalvar'=> "value",
  'arrayvar' => (object) ['1','2','3']]
);
