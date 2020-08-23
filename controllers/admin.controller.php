<?php

namespace Admin;



use AuthorModel\AuthorModel as AuthorModel;
use CategoryModel\CategoryModel as CategoryModel;
use BookModel\BookModel as BookModel;
use OrderModel\OrderModel as OrderModel;
use BannerModel\BannerModel as BannerModel;
use UserModel\UserModel as UserModel;
use Status\Status as Status;
use Pug\Facade as PugFacade;
use Pagination\Pagination as Pagination;

class AdminController
{
  public static function index()
  {
    echo PugFacade::displayFile('../views/admin/index.jade');
  }

  public static function orders()
  {
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    // Xác định có ?filter= hay không, nếu có thì
    // chuyển mục trong trang và đặt lại tiêu đề
    $filter = isset($_GET["filter"]) ? $_GET["filter"] : "";
    switch ($filter) {
      case 'pending':
        $card = 'pending';
        $title = 'Các đơn chờ xác nhận';
        $filter = 'p'; //sql keyword 
        break;
      case 'accepted':
        $card = 'accepted';
        $title = 'Các đơn chờ thanh toán';
        $filter = 'a';
        break;
      default:
        $card = false;
        $title = false;
        $filter = '%'; //lấy toàn bộ dữ liệu
    }

    // Xác định có từ khoá tìm kiếm hay không,
    $query = "";
    if (isset($_GET["query"]) && $_GET["query"] != "") {
      $query = $_GET["query"];
      $title = 'Tìm kiếm hoá đơn';
    }


    //Pagination
    try {
      $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
    } catch (\Exception $e) {
      $currentPage = 1;
    }
    $itemperpage = 5;

    $fetch = OrderModel::getOrders($filter, $query, $currentPage, $itemperpage);
    $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination

    $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
    $num_page = ceil($num_records / $itemperpage); //Số trang
    $pagination = Pagination::generate($currentPage, $num_page);
    if (!$fetch) {
      array_push($errors, "Có vấn đề xảy ra hoặc danh sách trống");
      $result = [];
    }

    echo PugFacade::displayFile('../views/admin/orders.jade', [
      'orders' => $result,
      'errors' => $errors,
      'messages' => $messages,
      'card' => $card, // Xác đỊnh mục nào đang được chọn
      'title' => $title,
      'search' => $query, // Lưu lại từ khoá và đưa vào mục tìm kiếm
      'pagination' => $pagination,
      'pagination_current_page' => $currentPage
    ]);
    exit();
  }


  public static function orderJSON($orderid)
  {
    $result = OrderModel::getOrder($orderid);
    $result["books"] =  OrderModel::getOrderDetail($orderid);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  public static function orderReject($orderid)
  {
    $result = OrderModel::rejectOrder($orderid);
    if ($result) {
      Status::addMessage("Đã từ chối đơn hàng có id " . $orderid);
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/orders');
    exit();
  }

  public static function orderAccept($orderid)
  {
    $result = OrderModel::acceptOrder($orderid);
    if ($result) {
      Status::addMessage("Đã chấp nhận đơn hàng có id " . $orderid);
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/orders');
    exit();
  }
  public static function orderComplete($orderid)
  {
    $result = OrderModel::completeOrder($orderid);
    if ($result) {
      Status::addMessage("Đã đánh dấu đơn hàng có id " . $orderid . " là đã thanh toán");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/orders');
    exit();
  }

  public static function orderError($orderid)
  {
    $result = OrderModel::makeOrderError($orderid);
    if ($result) {
      Status::addMessage("Đã đánh dấu đơn hàng có id " . $orderid . " là lỗi");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/orders');
    exit();
  }

  public static function users()
  {
    $errors = Status::getErrors();
    $messages = Status::getMessages();

    $filter = isset($_GET["filter"]) ? $_GET["filter"] : "";
    switch ($filter) {
      case 'disabled':
        $card = 'disabled';
        $title = 'Danh sách người dùng bị vô hiệu hoá';

        break;
      case 'admin':
        $card = 'admin';
        $title = 'Danh sách quản trị viên';
        break;
      default:
        $filter = false;
        $card = false;
        $title = "";
    }

    $query = "";
    if (isset($_GET["query"]) && $_GET["query"] != "") {
      $query = $_GET["query"];
      $title = 'Tìm kiếm người dùng';
    }

    //Pagination
    try {
      $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
    } catch (\Exception $e) {
      $currentPage = 1;
    }


    $itemperpage = 4;
    $fetch = UserModel::getUsers($filter, $query, $currentPage, $itemperpage);
    //Khởi tạo session
    if ($fetch == false) {
      array_push($errors, "Có vấn đề xảy ra hoặc danh sách trống");
      $result = [];
    }
    $result = $fetch['result'];

    $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
    $num_page = ceil($num_records / $itemperpage); //Số trang
    $pagination = Pagination::generate($currentPage, $num_page);


    echo PugFacade::displayFile('../views/admin/users.jade', [
      'users' => $result,
      'errors' => $errors,
      'messages' => $messages,
      'card' => $card, // Xác đỊnh mục nào đang được chọn
      'title' => $title,
      'search' => $query, // Lưu lại từ khoá và đưa vào mục tìm kiếm
      'pagination' => $pagination,
      'pagination_current_page' => $currentPage
    ]);
    exit();
  }

  public static function userDisable($userid)
  {
    $result = UserModel::disableUser($userid);
    if ($result) {
      Status::addMessage("Đã vô hiệu hoá người dùng có id " . $userid . ", người dùng sẽ không thể đăng nhập");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/users');
    exit();
  }
  public static function userEnable($userid)
  {
    $result = UserModel::enableUser($userid);
    if ($result) {
      Status::addMessage("Đã gỡ vô hiệu hoá người dùng có id " . $userid . ", giờ đây người dùng đã có thể đăng nhập");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/users');
    exit();
  }
  public static function userDelete($userid)
  {
    $result = UserModel::removeUser($userid);
    if ($result) {
      Status::addMessage("Đã xoá người dùng có id " . $userid . " khỏi cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/users');
    exit();
    // echo PugFacade::displayFile('../views/admin/users.jade');
  }


  public static function userMakeAdmin($userid)
  {
    $result = UserModel::makeUserAdmin($userid);
    if ($result) {
      Status::addMessage("Đã đặt người dùng có id " . $userid . " làm quản trị viên, giờ đây người này đã có thể quản trị website");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/users');
    exit();
  }
  public static function userRemoveAdmin($userid)
  {
    $result = UserModel::removeUserAdmin($userid);
    if ($result) {
      Status::addMessage("Đã xoá người dùng có id " . $userid . " khỏi quyền quản trị viên");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/users');
    exit();
  }

  public static function getUserJSON($userid)
  {
    $result = UserModel::getUserJSON($userid);
    if ($result) {
      echo json_encode($result, JSON_UNESCAPED_UNICODE);
      exit();
    } else {
      http_response_code(404);
      exit();
    }
  }


  public static function books()
  {
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    $title = false;

    $query = "";
    if (isset($_GET["query"]) && $_GET["query"] != "") {
      $query = $_GET["query"];
      $title = 'Tìm kiếm sách';
    }
    $authorid = "";
    if (isset($_GET["author"]) && $_GET["author"] != "") {
      $authorid = $_GET["author"];
      $title = 'Danh sách sách của tác giả';
    }

    $itemperpage = 7;
    try {
      $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
    } catch (\Exception $e) {
      $currentPage = 1;
    }

    $fetch = BookModel::getBooks($query, $authorid, $currentPage, $itemperpage);
    if ($fetch == false) {
      array_push($errors, "Có vấn đề xảy ra hoặc cơ sở dữ liệu trống");
    }
    $result = $fetch['result'];
    $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
    $num_page = ceil($num_records / $itemperpage); //Số trang
    $pagination = Pagination::generate($currentPage, $num_page);

    echo PugFacade::displayFile('../views/admin/books.jade', [
      'books' => $result,
      'errors' => $errors,
      'messages' => $messages,
      'title' => $title,
      'query' => $query,
      'pagination' => $pagination,
      'pagination_current_page' => $currentPage
    ]);

    exit();
  }
  public static function bookAdd()
  {
    $categories = CategoryModel::getCategories();
    $authors = AuthorModel::getAllAuthors();
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    echo PugFacade::displayFile('../views/admin/books.add.jade', [
      'authors' => $authors,
      'categories' => $categories,
      'errors' => $errors,
      'messages' => $messages
    ]);
  }

  //Dùng chung cho 2 trường hợp thêm mới và chỉnh sửa (mặc định là thêm mới) nên khi báo lỗi
  //THì nó sẽ redirect về địa chỉ được đưa vào
  //Nếu là edit thì click chọn đổi ảnh hay không thì reQuireImage sẽ kiểm soát được
  public static function postBookMiddleware($redirecturl = "/admin/books/add", $requireImage = true)
  {
    $errors = [];
    if (!isset($_POST["bookname"]) || $_POST["bookname"] == "") {
      array_push($errors, "Tên sách không được để trống");
    };
    if (!isset($_POST["bookpages"]) || $_POST["bookpages"] == "") {
      array_push($errors, "Số trang không được để trống");
    } else {
      if (intval($_POST["bookpages"]) < 0) {
        array_push($errors, "Số trang phải là số hợp lệ");
      }
    };

    if (!isset($_POST["bookweight"]) || $_POST["bookweight"] == "") {
      array_push($errors, "Trọng lượng sách không được để trống");
    } else {
      if (intval($_POST["bookweight"]) < 0) {
        array_push($errors, "Trọng lượng phải là số hợp lệ");
      }
    };
    if (!isset($_POST["categoryid"]) || $_POST["categoryid"] == "") {
      array_push($errors, "Phải chọn một danh mục");
    };

    if (!isset($_POST["authorid"]) || $_POST["authorid"] == "") {
      array_push($errors, "Phải chọn tác giả");
    };
    if (!isset($_POST["releasedate"]) || $_POST["releasedate"] == "") {
      array_push($errors, "Phải chọn thời gian phát hành");
    };
    if (!isset($_POST["description"]) || $_POST["description"] == "") {
      array_push($errors, "Sách phải có mô tả");
    };
    if (!isset($_POST["publisher"]) || $_POST["publisher"] == "") {
      array_push($errors, "Sách phải có nhà xuất bản");
    };
    if (!isset($_POST["bookbind"]) || $_POST["bookbind"] == "") {
      array_push($errors, "Sách phải có loại bìa");
    };
    if (!isset($_POST["bookprice"]) || $_POST["bookprice"] == "") {
      array_push($errors, "Phải nhập giá của sách");
    } else {
      if (intval($_POST["bookprice"]) < 0) {
        array_push($errors, "Giá của sách phải là số hợp lệ");
      }
    };;
    if (!isset($_POST["quantity"]) || $_POST["quantity"] == "") {
      array_push($errors, "Phải nhập số lượNg của sách");
    } else {
      if (intval($_POST["quantity"]) < 0) {
        array_push($errors, "Số lượng phải là số hợp lệ");
      }
    };;


    if ($requireImage == true) {
      if (!isset($_FILES['picture']['error']) || is_array($_FILES['picture']['error'] || $_FILES['picture']['error'] != UPLOAD_ERR_OK)) {
        array_push($errors, "Phải có ảnh minh hoạ cho sách");
      } else {
        if ($_FILES['picture']['size'] > 5242880) {
          array_push($errors, "Hình minh hoạ không được vượt quá 5MB");
        } else {
          $finfo = new \finfo(FILEINFO_MIME_TYPE);
          if (false === $ext = array_search(
            $finfo->file($_FILES['picture']['tmp_name']),
            array(
              'jpg' => 'image/jpeg',
              'png' => 'image/png',
              'gif' => 'image/gif',
            ),
            true
          )) {
            array_push($errors, "Phải có hình minh hoạ hoặc phải là ảnh");
          }
        }
      }
    }
    if (count($errors)) {
      Status::addErrors($errors);
      header('location: ' . $redirecturl);
      exit();
    }
  }

  //Tương tự như postBookMiddleware, xử lí việc thêm, sửa và fallback về 1 địa chỉ nếu có lỗi
  public static function postBookMoveFile($redirecturl = "/admin/books/add")
  {
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $ext = array_search(
      $finfo->file($_FILES['picture']['tmp_name']),
      array(
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
      ),
      true
    );
    $newfile = sha1_file($_FILES['picture']['tmp_name']) . "." . $ext;
    if (move_uploaded_file(
      $_FILES['picture']['tmp_name'],
      sprintf('../public/assets/img/books/' . $newfile)
    )) {
      return $newfile;
    } else {
      Status::addError("Có sự cố trong việc xử lí ảnh");
      header('location: ' . $redirecturl);
      exit();
    }
  }

  public static function postBookAdd()
  {
    self::postBookMiddleware();
    $bookname = $_POST["bookname"];
    $bookdescription = $_POST["description"];
    $bookpages = $_POST["bookpages"];
    $bookweight = $_POST["bookweight"];
    $releasedate = $_POST["releasedate"];
    $authorid = $_POST["authorid"];
    $categoryid = $_POST["categoryid"];
    $bookimageurl = self::postBookMoveFile();
    $bookprice = $_POST["bookprice"];
    $quantity = $_POST["quantity"];
    $publisher = $_POST["publisher"];
    $bookbind = $_POST["bookbind"];
    $result = BookModel::addBook($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl, $publisher, $bookbind);
    if ($result) {
      Status::addMessage("Đã thêm sách vào cơ sở dữ liệu");
    } else {
      Status::addError("Lỗi, không thể thêm sách, hãy thử lại");
    }
    header('location: /admin/books/add');
    exit();
  }

  public static function bookEdit($bookid)
  {
    $categories = CategoryModel::getCategories();
    $authors = AuthorModel::getAllAuthors();
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    $book = BookModel::getBook($bookid);
    echo PugFacade::displayFile('../views/admin/books.edit.jade', [
      'authors' => $authors,
      'categories' => $categories,
      'errors' => $errors,
      'messages' => $messages,
      'book' => $book
    ]);
  }

  public static function postBookEdit($bookid)
  {
    $replaceimage = isset($_POST["replaceimage"]);
    self::postBookMiddleware('/admin/books/' . $bookid . '/edit', $replaceimage);
    $bookname = $_POST["bookname"];
    $bookdescription = $_POST["description"];
    $bookpages = $_POST["bookpages"];
    $bookweight = $_POST["bookweight"];
    $releasedate = $_POST["releasedate"];
    $authorid = $_POST["authorid"];
    $categoryid = $_POST["categoryid"];
    $bookimageurl = $replaceimage ? self::postBookMoveFile('/admin/books/' . $bookid . '/edit') : false;
    $bookprice = $_POST["bookprice"];
    $quantity = $_POST["quantity"];
    $publisher = $_POST["publisher"];
    $bookbind = $_POST["bookbind"];
    echo $publisher;
    echo $bookbind;
    $result = BookModel::editBook($bookid, $bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl, $publisher, $bookbind);
    if ($result) {
      Status::addMessage("Đã chỉnh sửa thông tin của sách");
    } else {
      Status::addError("Lỗi, không thể chỉnh sửa sách, hãy thử lại");
    }
    header('location: /admin/books/' . $bookid . '/edit');
    exit();
  }

  public static function postBookEditSimpleMiddleware()
  {
    try {
      $errors = [];

      if (!isset($_POST["quantity"]) || $_POST["quantity"] == "" || intval($_POST["quantity"]) <= 0) {
        array_push($errors, "Số lượng nhập vào phải hợp lệ (chữ số, lớn hơn hoặc bằng 0)");
      }
      if (!isset($_POST["price"]) || $_POST["price"] == "" || intval($_POST["price"]) <= 0) {
        array_push($errors, "Giá nhập vào phải hợp lệ (chữ số, lớn hơn hoặc bằng 0)");
      }
      if (count($errors)) {
        Status::addErrors($errors);
        header('location: /admin/books');
        exit();
      }
    } catch (\Exception $e) {
      Status::addError("Số lượng nhập vào phải là số");
      header('location: /admin/books');
      exit();
    }
  }

  public static function postBookEditSimple($bookid)
  {
    self::postBookEditSimpleMiddleware();
    $quantity = intval($_POST["quantity"]);
    $price = intval($_POST["price"]);
    $result = BookModel::editBookSimple($bookid, $quantity, $price);
    if ($result) {
      Status::addMessage("Đã sửa số lượng và giá tiền " . $quantity . " cho sách " . $bookid);
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/books');
    exit();
  }


  public static function bookDelete($bookid)
  {
    $result = BookModel::removeBook($bookid);
    if ($result) {
      Status::addMessage("Đã xoá sách có id " . $bookid . " khỏi cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/books');
    exit();
  }


  public static function authors()
  {
    //Khởi tạo session
    $errors = Status::getErrors();
    $messages = Status::getMessages();

    $title = false;

    $query = "";
    if (isset($_GET["query"]) && $_GET["query"] != "") {
      $query = $_GET["query"];
      $title = 'Tìm kiếm tác giả';
    }
    $itemperpage = 3;
    try {
      $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
    } catch (\Exception $e) {
      $currentPage = 1;
    }


    $fetch = AuthorModel::getAuthors($query, $currentPage, $itemperpage);
    if (!$fetch) {
      array_push($errors, "Có vấn đề xảy ra xin vui lòng thử lại");
      $result = [];
    }

    $result = $fetch['result'];
    $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
    $num_page = ceil($num_records / $itemperpage); //Số trang
    $pagination = Pagination::generate($currentPage, $num_page);

    echo PugFacade::displayFile('../views/admin/authors.jade', [
      'authors' => $result,
      'errors' => $errors,
      'messages' => $messages,
      'query' => $query,
      'pagination' => $pagination,
      'pagination_current_page' => $currentPage
    ]);
    exit();
  }

  public static function authorFieldRequired()
  {
    if (!isset($_POST["name"]) || !isset($_POST["description"])) {
      //Nếu có post request gửi tới nhưng không có 2 trường
      //cần thiết thì quay về 
      header('location: /admin/authors');
    }
    $errors = [];
    if ($_POST["name"] == "") {
      array_push($errors, "Tên tác giả không được để trống");
    }
    if ($_POST["description"] == "") {
      array_push($errors, "Giới thiệu tác giả không được để trống");
    }
    if (count($errors)) {
      Status::addErrors($errors);
      header('location: /admin/authors');
      exit();
    }
  }

  public static function authorAdd()
  {
    self::authorFieldRequired();
    $name = $_POST["name"];
    $description = $_POST["description"];
    $result = AuthorModel::addAuthor($name, $description);
    if ($result) {
      Status::addMessage("Đã thêm vào cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/authors');
    exit();
  }
  public static function authorEdit($authorid)
  {
    self::authorFieldRequired();
    $name = $_POST["name"];
    $description = $_POST["description"];

    $result = AuthorModel::editAuthor($authorid, $name, $description);
    if ($result) {
      Status::addMessage("Đã sửa tác giả có id " . $authorid . " vào cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/authors');
    exit();
  }
  public static function authorDelete($authorid)
  {
    $result = AuthorModel::removeAuthor($authorid);
    if ($result) {
      Status::addMessage("Đã xoá tác giả có id " . $authorid . " khỏi cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/authors');
    exit();
  }

  //For Categories
  public static function categories()
  {
    $result = CategoryModel::getCategories();
    //Khởi tạo session
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    if ($result) {
      echo PugFacade::displayFile('../views/admin/categories.jade', [
        'categories' => $result,
        'errors' => $errors,
        'messages' => $messages
      ]);
    } else {
      array_push($errors, "Có vấn đề xảy ra xin vui lòng thử lại");
      echo PugFacade::displayFile('../views/admin/categories.jade', [
        'categories' => [],
        'errors' => $errors,
        'messages' => $messages
      ]);
    }
    exit();
  }

  public static function categoryFieldRequired()
  {
    if (!isset($_POST["name"]) || !isset($_POST["description"])) {
      //Nếu có post request gửi tới nhưng không có 2 trường
      //cần thiết thì quay về 
      header('location: /admin/categories');
    }
    if ($_POST["name"] === "") {
      Status::addErrors(['Tên danh mục không được để trống']);
      header('location: /admin/categories');
      exit();
    }
  }

  public static function categoryAdd()
  {
    self::categoryFieldRequired();
    $name = $_POST["name"];
    $result = CategoryModel::addCategories($name);
    if ($result) {
      Status::addMessage("Đã thêm vào cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/categories');
    exit();
  }

  public static function categoryEdit($categoryid)
  {
    self::categoryFieldRequired();
    $name = $_POST["name"];

    $result = CategoryModel::editCategories($categoryid, $name);
    if ($result) {
      Status::addMessage("Đã sửa danh mục ID: " . $categoryid . " vào cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/categories');
    exit();
  }

  public static function categoryDelete($categoryid)
  {
    $result = CategoryModel::removeCategories($categoryid);
    if ($result) {
      Status::addMessage("Đã xoá danh mục ID: " . $categoryid . " khỏi cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/categories');
    exit();
  }

  //For Banner
  public static function banners()
  {
    $result = BannerModel::getBanners();
    //Khởi tạo session
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    if ($result) {
      echo PugFacade::displayFile('../views/admin/banner.jade', [
        'banners' => $result,
        'errors' => $errors,
        'messages' => $messages
      ]);
    } else {
      array_push($errors, "Có vấn đề xảy ra xin vui lòng thử lại");
      echo PugFacade::displayFile('../views/admin/banner.jade', [
        'banners' => [],
        'errors' => $errors,
        'messages' => $messages
      ]);
    }
    exit();
  }

  public static function bannerAdd()
  {
    $books = BookModel::getAllBooks();
    $errors = Status::getErrors();
    $messages = Status::getMessages();
    echo PugFacade::displayFile('../views/admin/banner.add.jade', [
      'books' => $books,
      'errors' => $errors,
      'messages' => $messages
    ]);
  }

  public static function postBannerMiddleware($redirecturl = "/admin/banner/add", $requireImage = true)
  {
    $errors = [];
    if (!isset($_POST["bookid"]) || $_POST["bookid"] == "") {
      array_push($errors, "ID sách không được để trống");
    };

    if ($requireImage == true) {
      if (!isset($_FILES['picture']['error']) || is_array($_FILES['picture']['error'] || $_FILES['picture']['error'] != UPLOAD_ERR_OK)) {
        array_push($errors, "Phải có ảnh minh hoạ cho banner");
      } else {
        if ($_FILES['picture']['size'] > 5242880) {
          array_push($errors, "Hình minh hoạ không được vượt quá 5MB");
        } else {
          $finfo = new \finfo(FILEINFO_MIME_TYPE);
          if (false === $ext = array_search(
            $finfo->file($_FILES['picture']['tmp_name']),
            array(
              'jpg' => 'image/jpeg',
              'png' => 'image/png',
              'gif' => 'image/gif',
            ),
            true
          )) {
            array_push($errors, "Phải có hình minh hoạ hoặc phải là ảnh");
          }
        }
      }
    }
    if (count($errors)) {
      Status::addErrors($errors);
      header('location: ' . $redirecturl);
      exit();
    }
  }

  public static function postBannerMoveFile($redirecturl = "/admin/banner/add")
  {
    $finfo = new \finfo(FILEINFO_MIME_TYPE);
    $ext = array_search(
      $finfo->file($_FILES['picture']['tmp_name']),
      array(
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
      ),
      true
    );
    $newfile = sha1_file($_FILES['picture']['tmp_name']) . "." . $ext;
    if (move_uploaded_file(
      $_FILES['picture']['tmp_name'],
      sprintf('../public/assets/img/banner/' . $newfile)
    )) {
      return $newfile;
    } else {
      Status::addError("Có sự cố trong việc xử lí ảnh");
      header('location: ' . $redirecturl);
      exit();
    }
  }

  public static function postBannerAdd()
  {
    self::postBannerMiddleware();
    $bookid = $_POST["bookid"];;
    $customimage = self::postBannerMoveFile();
    $result = BannerModel::addbanner($bookid, $customimage);
    if ($result) {
      Status::addMessage("Đã thêm banner vào cơ sở dữ liệu");
    } else {
      Status::addError("Lỗi, không thể thêm banner, hãy thử lại");
    }
    header('location: /admin/banner/add');
    exit();
  }

  public static function bannerDelete($bookid)
  {
    $result = BannerModel::removeBanner($bookid);
    if ($result) {
      Status::addMessage("Đã xoá banner của sách có ID: " . $bookid . " khỏi cơ sở dữ liệu");
    } else {
      Status::addError("Có lỗi xảy ra, xin thử lại");
    }
    header('location: /admin/banner');
    exit();
  }
}
