<?php
use Pug\Facade as PugFacade;
$index = function() {
  echo PugFacade::displayFile('../views/home/index.jade');
}

?>