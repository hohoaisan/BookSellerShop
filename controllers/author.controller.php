<?php namespace Author;
    use Pug\Facade as PugFacade;
    use AuthorModel\AuthorModel as AuthorModel; 
    use BookModel\BookModel as BookModel;
    use Pagination\Pagination as Pagination;
class AuthorController {
    public static function index() {
        //Pagination
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (\Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 9;
    
        $fetch = AuthorModel::getAuthors(null, $currentPage, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
        $pagination = Pagination::generate($currentPage, $num_page);
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/author.jade', [
        'listAuthors' => $result,
        'num_records' => $num_records,
        'pagination' => $pagination,
        'pagination_current_page' => $currentPage
        ]);
        exit();
    } 

    public static function authorDetail($authorid){
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (\Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 12;
    
        $currentAuthor = AuthorModel::getAuthor($authorid);

        $fetch = BookModel::getBooksByAuthor($authorid, $currentPage, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
        $pagination = Pagination::generate($currentPage, $num_page);

        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/authorDetail.jade', [
        'listBooks' => $result,
        'author' => $currentAuthor,
        'pagination' => $pagination,
        'pagination_current_page' => $currentPage
        ]);
        exit();
        // echo PugFacade::displayFile('../views/home/authorDetail.jade');
    } 

}
