<?php
session_start();

include('../models/admin.model.php');
include('../actionStatus.php');

use AdminModel\AdminModel as AdminModel;
use Status\Status as Status;
use Pug\Facade as PugFacade;

$index = function () {
  echo PugFacade::displayFile('../views/admin/index.jade');
};

$orders = function () {
  //orders?name=&user=&sort=pending|accept|history
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$orderJSON = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$orderReject = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$orderAccept = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};
$orderComplete = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};
$orderReject = function () {
  echo PugFacade::displayFile('../views/admin/orders.jade');
};

$users = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};
$usersDisable = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};

$usersDelete = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};

$usersAdmin = function () {
  echo PugFacade::displayFile('../views/admin/users.jade');
};

$books = function () {
  $result = AdminModel::getBooks();
  //Khởi tạo session
  $errors = Status::getErrors();
  $messages = Status::getMessages();
  if ($result) {
    echo PugFacade::displayFile('../views/admin/books.jade', [
      'books' => $result,
      'errors' => $errors,
      'messages' => $messages
    ]);
  } else {
    array_push($errors, "Có vấn đề xảy ra hoặc cơ sở dữ liệu trống");
    echo PugFacade::displayFile('../views/admin/books.jade', [
      'books' => [],
      'errors' => $errors,
      'messages' => $messages
    ]);
  }
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
    header('location: '.$redirecturl);
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
    header('location: '.$redirecturl);
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
  $postBookMiddleware('/admin/books/'.$bookid.'/edit', $replaceimage);
  $bookname = $_POST["bookname"];
  $bookdescription = $_POST["description"];
  $bookpages = $_POST["bookpages"];
  $bookweight = $_POST["bookweight"];
  $releasedate = $_POST["releasedate"];
  $authorid = $_POST["authorid"];
  $categoryid = $_POST["categoryid"];
  $bookimageurl = $replaceimage?$postBookMoveFile('/admin/books/'.$bookid.'/edit'):false;
  $bookprice = $_POST["bookprice"];
  $quantity = $_POST["quantity"]; 
  $result = AdminModel::editBook($bookid, $bookname, $bookdescription, $bookpages, $bookweight, $releasedate, $authorid, $categoryid, $bookprice, $quantity, $bookimageurl);
  if ($result) {
    Status::addMessage("Đã chỉnh sửa thông tin của sách");
  } else {
    Status::addError("Lỗi, không thể chỉnh sửa sách, hãy thử lại");
  }
  header('location: /admin/books/'.$bookid.'/edit');
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
