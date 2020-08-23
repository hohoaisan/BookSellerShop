<?php namespace Category;

    use Pug\Facade as PugFacade;
    use Pagination\Pagination as Pagination;
    use CategoryModel\CategoryModel as CategoryModel;  
    use BookModel\BookModel as BookModel;  
    class CategoryController {

    
    public static function index () {
        header('location: /categories/1');
    }
    public static function categoryBook ($categoryid){ 
        //Pagination
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (\Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 12;
    
        $listCategories = CategoryModel::getCategories();
        $currentCategory = CategoryModel::getSingleCategory($categoryid);

        $fetch = BookModel::getBooksByCategory($categoryid, $currentPage, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
        $pagination = Pagination::generate($currentPage, $num_page);
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/category.jade', [
        'listCategories' => $listCategories,
        'listBooks' => $result,
        'category' => $currentCategory,
        'pagination' => $pagination,
        'pagination_current_page' => $currentPage
        ]);
        exit();
    }

}
