<?php namespace Category;

    use Pug\Facade as PugFacade;
    use CategoryModel\CategoryModel as CategoryModel;  
    use BookModel\BookModel as BookModel;  
    class CategoryController {

    
    public static function index () {
        header('location: /categories/1');
    }
// TODO:Pagination?
    public static function removeParam ($param) {
        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
        if (strpos($url, '?')) {
            return $url . '&';
        } else return $url . '?';
    }
    public static function categoryBook ($categoryid){ 
        //Pagination
        try {
            $page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (\Exception $e) {
            $page = 1;
        }
        $itemperpage = 12;
    
        $listCategories = CategoryModel::getCategories();
        $currentCategory = CategoryModel::getSingleCategory($categoryid);

        $fetch = BookModel::getBooksByCategory($categoryid, $page, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
    
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/category.jade', [
        'listCategories' => $listCategories,
        'listBooks' => $result,
        'category' => $currentCategory,
        'pagination_url' => self::removeParam('page'), //Lấy url cũ và render mới
        'pagination_pages' => $num_page,
        'pagination_current_page' => $page
        ]);
        exit();
    }

}
