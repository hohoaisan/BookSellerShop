<?php

$removeParam = function ($param) {
  $url = $_SERVER['REQUEST_URI'];
  $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
  $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
  if (strpos($url, '?')) {
      return $url . '&';
  } else return $url . '?';
};


$paginationGenerator = function ($currentPage, $num_page, $chunk = 5) use ($removeParam) {
  //Mặc định chunk là 5, chỉ có 5 số được xuất hiện
  $pagination = [];
  if ($num_page > 1) {
      $url = $removeParam('page');
      for ($i = 1; $i <= $num_page; $i++) {
          array_push($pagination, [
              'index' => $i,
              'url' => $url . 'page=' . $i
          ]);
      }
      $offset = $currentPage - $chunk / 2;
      if ($currentPage == $num_page) {
          $offset = $num_page - $chunk;
      }

      if ($offset < 0) {
          $offset = 0;
      }
      $pagination = array_slice($pagination, $offset, $chunk);
      if (intval($currentPage) != 1) {
          array_unshift($pagination, [
              'index' => '<',
              'url' => $url . 'page=' . (intval($currentPage) - 1)
          ]);
      }
      if (intval($currentPage) != $num_page) {
          array_push($pagination, [
              'index' => '>',
              'url' => $url . 'page=' . (intval($currentPage) + 1)
          ]);
      }
      if ($num_page > $chunk) {
          if (intval($currentPage) != 1) {
              array_unshift($pagination, [
                  'index' => '<<',
                  'url' => $url . 'page=1'
              ]);
          }
          if (intval($currentPage) != $num_page) {
              array_push($pagination, [
                  'index' => '>>',
                  'url' => $url . 'page=' . $num_page
              ]);
          }
      }
  }
  return $pagination;
};


?>