<?php 
    use Pug\Facade as PugFacade;
    use AuthorModel\AuthorModel as AuthorModel; 
    use BookModel\BookModel as BookModel;
    use Pagination\Pagination as Pagination;

    $removeParam = function ($param) {
        $url = $_SERVER['REQUEST_URI'];
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
        $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
        if (strpos($url, '?')) {
            return $url . '&';
        } else return $url . '?';
    };

    $index = function() {
        //Pagination
        try {
            $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (Exception $e) {
            $currentPage = 1;
        }
        $itemperpage = 6;
    
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
    };

    $authorDetail = function($authorid) use ($removeParam){
        try {
            $page = intval(isset($_GET['page']) ? $_GET['page'] : 1);
        } catch (Exception $e) {
            $page = 1;
        }
        $itemperpage = 12;
    
        $currentAuthor = AuthorModel::getAuthor($authorid);

        $fetch = BookModel::getBooksByAuthor($authorid, $page, $itemperpage);
        $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination
    
        $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
        $num_page = ceil($num_records / $itemperpage); //Số trang
    
        if (!$fetch) {
            $result = [];
        }
    
        echo PugFacade::displayFile('../views/home/authorDetail.jade', [
        'listBooks' => $result,
        'author' => $currentAuthor,
        'pagination_url' => $removeParam('page'), //Lấy url cũ và render mới
        'pagination_pages' => $num_page,
        'pagination_current_page' => $page
        ]);
        exit();
        // echo PugFacade::displayFile('../views/home/authorDetail.jade');
    };
?>