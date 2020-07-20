<?php
include('../models/admin.model.php');

use AdminModel\AdminModel as AdminModel;
use Status\Status as Status;
use Pug\Facade as PugFacade;


$removeParam = function ($param) {
  $url = $_SERVER['REQUEST_URI'];
  $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
  $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
  if (strpos($url, '?')) {
    return $url . '&';
  } else return $url . '?';
};

$paginationGenerator = function($currentPage,$num_page, $chunk = 5) use($removeParam) {
  //Mặc định chunk là 5, chỉ có 5 số được xuất hiện
  $pagination = [];
  if ($num_page > 1) {
    $url = $removeParam('page');
    for ($i = 1; $i <= $num_page; $i++) {
      array_push($pagination, [
        'index' => $i,
        'url' => $url . 'page=' . $i
      ]);
    }
    $offset = $currentPage - $chunk / 2;
    if ($currentPage == $num_page) {
      $offset = $num_page - $chunk;
    }

    if ($offset < 0) {
      $offset = 0;
    }
    $pagination = array_slice($pagination, $offset, $chunk);
    if (intval($currentPage) != 1) {
      array_unshift($pagination, [
        'index' => '<',
        'url' => $url . 'page=' . (intval($currentPage)-1)
      ]);
    }
    if (intval($currentPage) != $num_page) {
      array_push($pagination, [
        'index' => '>',
        'url' => $url . 'page=' . (intval($currentPage)+1)
      ]);
    }
    if ($num_page > $chunk) {
      if (intval($currentPage) != 1) {
        array_unshift($pagination, [
          'index' => '<<',
          'url' => $url . 'page=1'
        ]);
      }
      if (intval($currentPage) != $num_page) {
        array_push($pagination, [
          'index' => '>>',
          'url' => $url . 'page=' . $num_page
        ]);
      }
    }
    
  }
  return $pagination;
};
$index = function () {
  echo PugFacade::displayFile('../views/admin/index.jade');
};

$orders = function () use ($paginationGenerator) {
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
  } catch (Exception $e) {
    $currentPage = 1;
  }
  $itemperpage = 5;

  $fetch = AdminModel::getOrders($filter, $query, $currentPage, $itemperpage);
  $result = $fetch['result']; //Lấy kết quả trong 1 trang pagination

  $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
  $num_page = ceil($num_records / $itemperpage); //Số trang
  $pagination = $paginationGenerator($currentPage, $num_page);
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
};


$orderJSON = function ($orderid) {
  $result = AdminModel::getOrder($orderid);
  $result["books"] =  AdminModel::getOrderDetail($orderid);
  echo json_encode($result, JSON_UNESCAPED_UNICODE);
};

$orderReject = function ($orderid) {
  $result = AdminModel::rejectOrder($orderid);
  if ($result) {
    Status::addMessage("Đã từ chối đơn hàng có id " . $orderid);
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/orders');
  exit();
};

$orderAccept = function ($orderid) {
  $result = AdminModel::acceptOrder($orderid);
  if ($result) {
    Status::addMessage("Đã chấp nhận đơn hàng có id " . $orderid);
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/orders');
  exit();
};
$orderComplete = function ($orderid) {
  $result = AdminModel::completeOrder($orderid);
  if ($result) {
    Status::addMessage("Đã đánh dấu đơn hàng có id " . $orderid . " là đã thanh toán");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/orders');
  exit();
};

$orderError = function ($orderid) {
  $result = AdminModel::makeOrderError($orderid);
  if ($result) {
    Status::addMessage("Đã đánh dấu đơn hàng có id " . $orderid . " là lỗi");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/orders');
  exit();
};



$users = function () use ($paginationGenerator) {
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
  } catch (Exception $e) {
    $currentPage = 1;
  }
  
  
  $itemperpage = 4;
  $fetch = AdminModel::getUsers($filter, $query, $currentPage, $itemperpage);
  //Khởi tạo session
  if ($fetch == false) {
    array_push($errors, "Có vấn đề xảy ra hoặc danh sách trống");
    $result = [];
  }
  $result = $fetch['result'];

  $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
  $num_page = ceil($num_records / $itemperpage); //Số trang
  $pagination = $paginationGenerator($currentPage, $num_page);


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
};
$userDisable = function ($userid) {
  $result = AdminModel::disableUser($userid);
  if ($result) {
    Status::addMessage("Đã vô hiệu hoá người dùng có id " . $userid . ", người dùng sẽ không thể đăng nhập");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/users');
  exit();
};
$userEnable = function ($userid) {
  $result = AdminModel::enableUser($userid);
  if ($result) {
    Status::addMessage("Đã gỡ vô hiệu hoá người dùng có id " . $userid . ", giờ đây người dùng đã có thể đăng nhập");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/users');
  exit();
};
$userDelete = function ($userid) {
  $result = AdminModel::removeUser($userid);
  if ($result) {
    Status::addMessage("Đã xoá người dùng có id " . $userid . " khỏi cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/users');
  exit();
  // echo PugFacade::displayFile('../views/admin/users.jade');
};


$userMakeAdmin = function ($userid) {
  $result = AdminModel::makeUserAdmin($userid);
  if ($result) {
    Status::addMessage("Đã đặt người dùng có id " . $userid . " làm quản trị viên, giờ đây người này đã có thể quản trị website");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/users');
  exit();
};
$userRemoveAdmin = function ($userid) {
  $result = AdminModel::removeUserAdmin($userid);
  if ($result) {
    Status::addMessage("Đã xoá người dùng có id " . $userid . " khỏi quyền quản trị viên");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/users');
  exit();
};

$getUserJSON = function ($userid) {
  $result = AdminModel::getUserJSON($userid);
  if ($result) {
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit();
  } else {
    http_response_code(404);
    exit();
  }
};


$books = function () use($paginationGenerator) {
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

  $itemperpage = 3;
  try {
    $currentPage = intval(isset($_GET['page']) ? $_GET['page'] : 1);
  } catch (Exception $e) {
    $currentPage = 1;
  }

  $fetch = AdminModel::getBooks($query, $authorid, $currentPage, $itemperpage);
  if ($fetch == false) {
    array_push($errors, "Có vấn đề xảy ra hoặc cơ sở dữ liệu trống");
  }
  $result = $fetch['result'];
  $num_records = $fetch['rowcount']; //Lấy số kết quả trong toàn bộ bảng
  $num_page = ceil($num_records / $itemperpage); //Số trang
  $pagination = $paginationGenerator($currentPage, $num_page);

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
};
$bookAdd = function () {
  $categories = AdminModel::getCategories();
  $authors = AdminModel::getAuthors();
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  echo PugFacade::displayFile('../views/admin/books.add.jade', [
    'authors' => $authors,
    'categories' => $categories,
    'errors' => $errors,
    'messages' => $messages
  ]);
};

//Dùng chung cho 2 trường hợp thêm mới và chỉnh sửa (mặc định là thêm mới) nên khi báo lỗi
//THì nó sẽ redirect về địa chỉ được đưa vào
//Nếu là edit thì click chọn đổi ảnh hay không thì reQuireImage sẽ kiểm soát được
$postBookMiddleware =  function ($redirecturl = "/admin/books/add", $requireImage = true) {
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
        $finfo = new finfo(FILEINFO_MIME_TYPE);
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
};

//Tương tự như postBookMiddleware, xử lí việc thêm, sửa và fallback về 1 địa chỉ nếu có lỗi
$postBookMoveFile = function ($redirecturl = "/admin/books/add") {
  $finfo = new finfo(FILEINFO_MIME_TYPE);
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
};

$postBookAdd = function () use ($postBookMiddleware, $postBookMoveFile) {
  $postBookMiddleware();
  $bookname = $_POST["bookname"];
  $bookdescription = $_POST["description"];
  $bookpages = $_POST["bookpages"];
  $bookweight = $_POST["bookweight"];
  $releasedate = $_POST["releasedate"];
  $authorid = $_POST["authorid"];
  $categoryid = $_POST["categoryid"];
  $bookimageurl = $postBookMoveFile();
  $bookprice = $_POST["bookprice"];
  $quantity = $_POST["quantity"];
  $result = AdminModel::addBook($bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl);
  if ($result) {
    Status::addMessage("Đã thêm sách vào cơ sở dữ liệu");
  } else {
    Status::addError("Lỗi, không thể thêm sách, hãy thử lại");
  }
  header('location: /admin/books/add');
  exit();
};

$bookEdit = function ($bookid) {
  $categories = AdminModel::getCategories();
  $authors = AdminModel::getAuthors();
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  $book = AdminModel::getBook($bookid);
  echo PugFacade::displayFile('../views/admin/books.edit.jade', [
    'authors' => $authors,
    'categories' => $categories,
    'errors' => $errors,
    'messages' => $messages,
    'book' => $book
  ]);
};

$postBookEdit = function ($bookid) use ($postBookMiddleware, $postBookMoveFile) {
  $replaceimage = isset($_POST["replaceimage"]);
  $postBookMiddleware('/admin/books/' . $bookid . '/edit', $replaceimage);
  $bookname = $_POST["bookname"];
  $bookdescription = $_POST["description"];
  $bookpages = $_POST["bookpages"];
  $bookweight = $_POST["bookweight"];
  $releasedate = $_POST["releasedate"];
  $authorid = $_POST["authorid"];
  $categoryid = $_POST["categoryid"];
  $bookimageurl = $replaceimage ? $postBookMoveFile('/admin/books/' . $bookid . '/edit') : false;
  $bookprice = $_POST["bookprice"];
  $quantity = $_POST["quantity"];
  $result = AdminModel::editBook($bookid, $bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl);
  if ($result) {
    Status::addMessage("Đã chỉnh sửa thông tin của sách");
  } else {
    Status::addError("Lỗi, không thể chỉnh sửa sách, hãy thử lại");
  }
  header('location: /admin/books/' . $bookid . '/edit');
  exit();
};

$postBookEditSimpleMiddleware = function () {
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
  } catch (Exception $e) {
    Status::addError("Số lượng nhập vào phải là số");
    header('location: /admin/books');
    exit();
  }
};

$postBookEditSimple = function ($bookid) use ($postBookEditSimpleMiddleware) {
  $postBookEditSimpleMiddleware();
  $quantity = intval($_POST["quantity"]);
  $price = intval($_POST["price"]);
  $result = AdminModel::editBookSimple($bookid, $quantity, $price);
  if ($result) {
    Status::addMessage("Đã sửa số lượng và giá tiền " . $quantity . " cho sách " . $bookid);
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/books');
  exit();
};


$bookDelete = function ($bookid) {
  $result = AdminModel::removeBook($bookid);
  if ($result) {
    Status::addMessage("Đã xoá sách có id " . $bookid . " khỏi cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/books');
  exit();
};


$authors = function () {
  $result = AdminModel::getAuthors();
  //Khởi tạo session
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  if ($result) {
    echo PugFacade::displayFile('../views/admin/authors.jade', [
      'authors' => $result,
      'errors' => $errors,
      'messages' => $messages
    ]);
  } else {
    array_push($errors, "Có vấn đề xảy ra xin vui lòng thử lại");
    echo PugFacade::displayFile('../views/admin/authors.jade', [
      'authors' => [],
      'errors' => $errors,
      'messages' => $messages
    ]);
  }
  exit();
};

$authorFieldRequired = function () use ($authors) {
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
};

$authorAdd = function () use ($authorFieldRequired) {
  $authorFieldRequired();
  $name = $_POST["name"];
  $description = $_POST["description"];
  $result = AdminModel::addAuthor($name, $description);
  if ($result) {
    Status::addMessage("Đã thêm vào cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/authors');
  exit();
};
$authorEdit = function ($authorid) use ($authorFieldRequired) {
  $authorFieldRequired();
  $name = $_POST["name"];
  $description = $_POST["description"];

  $result = AdminModel::editAuthor($authorid, $name, $description);
  if ($result) {
    Status::addMessage("Đã sửa tác giả có id " . $authorid . " vào cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/authors');
  exit();
};
$authorDelete = function ($authorid) {
  $result = AdminModel::removeAuthor($authorid);
  if ($result) {
    Status::addMessage("Đã xoá tác giả có id " . $authorid . " khỏi cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/authors');
  exit();
};

//For Categories
$categories = function () {
  $result = AdminModel::getCategories();
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
};

$categoryFieldRequired = function () use ($categories) {
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
};

$categoryAdd = function () use ($categoryFieldRequired) {
  $categoryFieldRequired();
  $name = $_POST["name"];
  $result = AdminModel::addCategories($name);
  if ($result) {
    Status::addMessage("Đã thêm vào cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/categories');
  exit();
};

$categoryEdit = function ($categoryid) use ($categoryFieldRequired) {
  $categoryFieldRequired();
  $name = $_POST["name"];

  $result = AdminModel::editCategories($categoryid, $name);
  if ($result) {
    Status::addMessage("Đã sửa danh mục ID: " . $categoryid . " vào cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/categories');
  exit();
};

$categoryDelete = function ($categoryid) {
  $result = AdminModel::removeCategories($categoryid);
  if ($result) {
    Status::addMessage("Đã xoá danh mục ID: " . $categoryid . " khỏi cơ sở dữ liệu");
  } else {
    Status::addError("Có lỗi xảy ra, xin thử lại");
  }
  header('location: /admin/categories');
  exit();
};
