<?php 
    include('../models/author.model.php');
    use Pug\Facade as PugFacade;
    use AuthorModel\AuthorModel as AuthorModel; 

    $removeParam = function ($param) {
        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
        if (strpos($url, '?')) {
            return $url . '&';
        } else return $url . '?';
    };

    $index = function() use ($removeParam){
        //Pagination
        try {
            $page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (Exception $e) {
            $page = 1;
        }
        $itemperpage = 6;
    
        $fetch = AuthorModel::getAllAuthor($page, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
    
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/author.jade', [
        'listAuthors' => $result,
        'pagination_url' => $removeParam('page'), //Lấy url cũ và render mới
        'pagination_pages' => $num_page,
        'pagination_current_page' => $page
        ]);
        exit();
    }
?>