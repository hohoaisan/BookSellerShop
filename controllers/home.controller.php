<?php
include('../models/home.model.php');
include('../modules/pagination.php');


use HomePage\HomePage as HomePage;
use Phug\Lexer\State;
use Status\Status as Status;
use Pug\Facade as PugFacade;


$index = function () use ($paginationGenerator) {
    //Pagination
    try {
        $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
    } catch (Exception $e) {
        $currentPage = 1;
    }
    $itemperpage = 18;

    $fetch = HomePage::getShowBooks($currentPage, $itemperpage);
    $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination

    $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
    $num_page = ceil($num_records / $itemperpage); //Số trang
    $pagination = $paginationGenerator($currentPage, $num_page);

    if (!$fetch) {
        $result = [];
    }
    $listBooks = HomePage::getBooks();
    $listCategories = HomePage::getCategory();
    $mostSeller = HomePage::mostSeller();
    $mostPopular = HomePage::mostPopular();
    $banner = HomePage::getBanner();

    echo PugFacade::displayFile('../views/home/index.jade', [
        'listBooks' => $listBooks,
        'listCategories' => $listCategories,
        'mostSeller' => $mostSeller,
        'mostPopular' => $mostPopular,
        'showBooks' => $result, // Xác đỊnh mục nào đang được chọn
        'pagination' => $pagination,
        'pagination_current_page' => $currentPage,
        'banner' => $banner
    ]);
    exit();
};
