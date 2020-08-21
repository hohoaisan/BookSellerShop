<?php

    include('../models/book.model.php');
    include('../modules/pagination.php');
    include_once('../models/rating.model.php');
    use RatingModel\RatingModel as RatingModel;
    use Pug\Facade as PugFacade;
    use BookModel\BookModel as BookModel;   
    

    $index = function () use ($paginationGenerator){ 
        $query = "";
        if (isset($_GET["query"]) && $_GET["query"] != "") {
            $query = $_GET["query"];
        }
        //Pagination
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 12;
    
        $fetch = BookModel::getFullBooks($query, $currentPage, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
        $pagination = $paginationGenerator($currentPage, $num_page);
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/books.jade', [
        'listBooks' => $result,// Xác đỊnh mục nào đang được chọn
        'search' => $query, // Lưu lại từ khoá và đưa vào mục tìm kiếm
        'pagination' => $pagination,
        'pagination_current_page' => $currentPage
        ]);
        exit();
    };

    $bookDetail = function($bookid) use ($paginationGenerator){
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 2;

        $book = BookModel::getBookDetail($bookid);
        $related = BookModel::getBookRelated($bookid);
        $fetch_ratings = RatingModel::getBookRatings($bookid,  $currentPage, $itemperpage);
        $ratings = $fetch_ratings['result'];
        $num_records = $fetch_ratings['rowcount'];
        $num_page = ceil($num_records / $itemperpage);
        $pagination = $paginationGenerator($currentPage, $num_page);
        shuffle($related);
        echo PugFacade::displayFile('../views/home/bookDetail.jade',[
            'book' => $book,
            'related'=> $related,
            'ratings' => $ratings,
            'pagination' => $pagination,
            'pagination_current_page' => $currentPage
        ]);
        exit();
    };
