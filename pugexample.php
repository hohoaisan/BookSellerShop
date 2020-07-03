<?php
include_once('vendor/autoload.php');

use Pug\Facade as PugFacade;

// Những gì render được sẽ tràn ra ngoài file php chưa hàm render
PugFacade::displayFile('views/example.pug', [
  'normalvar'=> "value",
  'arrayvar' => (object) ['1','2','3']]
);

// Những gì render được sẽ được trả về 1 biến kết quả, để return hoặc echo đều được
PugFacade::display('views/example.pug', [
  'normalvar'=> "value",
  'arrayvar' => (object) ['1','2','3']]
);