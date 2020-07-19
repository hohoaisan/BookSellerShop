<?php
    include('../models/book.model.php');
    use Pug\Facade as PugFacade;
    use BookModel\BookModel as BookModel;   
    
    $removeParam = function ($param) {
        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
        if (strpos($url, '?')) {
            return $url . '&';
        } else return $url . '?';
    };

    $index = function () use ($removeParam){ 
        $query = "";
        if (isset($_GET["query"]) && $_GET["query"] != "") {
            $query = $_GET["query"];
        }
        //Pagination
        try {
            $page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (Exception $e) {
            $page = 1;
        }
        $itemperpage = 12;
    
        $fetch = BookModel::getFullBooks($query, $page, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
    
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/books.jade', [
        'listBooks' => $result,// Xác đỊnh mục nào đang được chọn
        'search' => $query, // Lưu lại từ khoá và đưa vào mục tìm kiếm
        'pagination_url' => $removeParam('page'), //Lấy url cũ và render mới
        'pagination_pages' => $num_page,
        'pagination_current_page' => $page
        ]);
        exit();
    };

    $bookDetail = function($bookid){
        $book = BookModel::getBookDetail($bookid);
        echo PugFacade::displayFile('../views/home/bookDetail.jade',[
            'book' => $book
        ]);
        exit();
    }


?>