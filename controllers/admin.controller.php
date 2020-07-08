<?php

use Pug\Facade as PugFacade;
$index = function() {
  echo PugFacade::displayFile('../views/admin/index.jade');
}
?>