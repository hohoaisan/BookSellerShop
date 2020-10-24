# BookSellerShop
A small website for selling and managing books written in PHP

## Features
Will be updated soon...

## Dependencies
+ XAMPP > 7.2
+ PHP > 7
+ MySQL > 6

## Running
+ Config database in /models/connect.php (utf8mb4_unicode_ci is recommended)
+ Set document root and directory of XAMPP's default `httpd.conf` to:
```
DocumentRoot "<repository_downloaded_path>/public"
<Directory "<repository_downloaded_path>/public">
```
*Note: this setting is mandatory for making the project works fine*
+ Load the bookseller.sql into configured database
+ Turn on MySql and PhpMyAdmin and enjoy!
