<?php namespace Home;


use CategoryModel\CategoryModel as CategoryModel;
use Pug\Facade as PugFacade;
use BannerModel\BannerModel as BannerModel;
use BookModel\BookModel as BookModel;
use Pagination\Pagination as Pagination;

class HomeController {
    public static function index() {
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (\Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 18;
    
        $fetch = BookModel::getLastestBooks($currentPage, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
        $pagination = Pagination::generate($currentPage, $num_page);
    
        if (!$fetch) {
            $result = [];
        }
        $listCategories = CategoryModel::getLimitedCategory();
        $mostSeller = BookModel::getMostSeller();
        $mostPopular = BookModel::getMostPopular();
        $banner = BannerModel::getBanners();
    
        echo PugFacade::displayFile('../views/home/index.jade', [
            'listCategories' => $listCategories,
            'mostSeller' => $mostSeller,
            'mostPopular' => $mostPopular,
            'lastestBooks' => $result, // Xác đỊnh mục nào đang được chọn
            'pagination' => $pagination,
            'pagination_current_page' => $currentPage,
            'banner' => $banner
        ]);
        exit();
    }
    
}