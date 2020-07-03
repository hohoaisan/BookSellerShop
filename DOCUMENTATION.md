# Documentations
## Hướng dẫn chạy server
0. PHP 5.3 trở lên
1. Clone repository này về tại thư mục htdocs
2. Config cho httpd.conf của Apache:
```
DocumentRoot "C:/xampp/htdocs/BookSellerShop/public"
<Directory "C:/xampp/htdocs/BookSellerShop/public">
```
> Lưu ý: Có thể thay C:/xampp bằng địa chỉ cài đặt xampp khác để không bị lỗi khi chạy project khác
3. Restart apache

## Project explain

### routers
Chứa các router dẫn các yêu cầu người dùng đến các controller phù hợp

### controllers
Chứa các hàm xử lí cụ thể về một đối tượng nào đó mà người dùng yêu cầu thông qua routers

### public
Chứa các file tĩnh có thể truy cập trong server mà không thông qua router (ví dụ /styles/style.css)

### models
Chứa các module tương tác với cơ sở dữ liệu

### views
Chứa các layout, template hay giao diện mà người dùng sẽ được thấy

## Phug (Pug-php)
[Website](https://phug-lang.com/), [Cached](https://web.archive.org/web/20180823052643/https://phug-lang.com/)

Là một html preproccessor nhằm hỗ trợ cho việc render html và giúp người phát triển viết code nhanh hơn, cấu trúc rõ ràng hơn và dễ bảo trì code hơn

Mẫu [link](https://codepen.io/hohoaisan/pen/eYJGagG)

## Bramus Router
[Github](https://github.com/bramus/router)

Tạo router đơn giản cho PHP
