/*
 Navicat MySQL Data Transfer

 Source Server         : MYSQL
 Source Server Type    : MySQL
 Source Server Version : 100413
 Source Host           : localhost:3306
 Source Schema         : bookseller

 Target Server Type    : MySQL
 Target Server Version : 100413
 File Encoding         : 65001

 Date: 08/07/2020 01:32:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for authors
-- ----------------------------
DROP TABLE IF EXISTS `authors`;
CREATE TABLE `authors`  (
  `authorid` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `authorname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `authordescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`authorid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for banner
-- ----------------------------
DROP TABLE IF EXISTS `banner`;
CREATE TABLE `banner`  (
  `bookid` int(6) UNSIGNED ZEROFILL NOT NULL,
  `customimage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`bookid`) USING BTREE,
  INDEX `fkIdx_97`(`bookid`) USING BTREE,
  CONSTRAINT `FK_97` FOREIGN KEY (`bookid`) REFERENCES `books` (`bookid`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for books
-- ----------------------------
DROP TABLE IF EXISTS `books`;
CREATE TABLE `books`  (
  `bookid` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `bookname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookimageurl` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookdescription` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bookpages` tinyint NOT NULL DEFAULT 0,
  `bookweight` float NOT NULL DEFAULT 0,
  `releasedate` date NOT NULL,
  `viewcount` mediumint NOT NULL DEFAULT 0,
  `purchasedcount` mediumint NOT NULL DEFAULT 0,
  `authorid` int(6) UNSIGNED ZEROFILL NOT NULL,
  `categoryid` int(2) UNSIGNED ZEROFILL NOT NULL,
  `quantity` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`bookid`) USING BTREE,
  INDEX `fkIdx_37`(`authorid`) USING BTREE,
  INDEX `fkIdx_40`(`categoryid`) USING BTREE,
  CONSTRAINT `FK_37` FOREIGN KEY (`authorid`) REFERENCES `authors` (`authorid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_40` FOREIGN KEY (`categoryid`) REFERENCES `categories` (`categoryid`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories`  (
  `categoryid` int(2) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `categoryname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`categoryid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments`  (
  `commentid` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `timestamp` datetime(0) NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `usersid` int(6) UNSIGNED ZEROFILL NOT NULL,
  `bookid` int(6) UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`commentid`) USING BTREE,
  INDEX `fkIdx_48`(`usersid`) USING BTREE,
  INDEX `fkIdx_51`(`bookid`) USING BTREE,
  CONSTRAINT `FK_48` FOREIGN KEY (`usersid`) REFERENCES `users` (`usersid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_51` FOREIGN KEY (`bookid`) REFERENCES `books` (`bookid`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for district
-- ----------------------------
DROP TABLE IF EXISTS `district`;
CREATE TABLE `district`  (
  `id` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `pid` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `pid`(`pid`) USING BTREE,
  CONSTRAINT `district_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `province` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders`  (
  `orderid` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `usersid` int(6) UNSIGNED ZEROFILL NOT NULL,
  `orderstatus` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'p',
  `timestamp` datetime(0) NOT NULL DEFAULT current_timestamp(0),
  `addressid` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `addresstext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `totalmoney` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`orderid`) USING BTREE,
  INDEX `fkIdx_58`(`usersid`) USING BTREE,
  INDEX `fk_orders_ward_1`(`addressid`) USING BTREE,
  CONSTRAINT `FK_58` FOREIGN KEY (`usersid`) REFERENCES `users` (`usersid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_orders_ward_1` FOREIGN KEY (`addressid`) REFERENCES `ward` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for ordersdetails
-- ----------------------------
DROP TABLE IF EXISTS `ordersdetails`;
CREATE TABLE `ordersdetails`  (
  `qtyordered` int NOT NULL,
  `amount` double NOT NULL,
  `orderid` int(6) UNSIGNED ZEROFILL NOT NULL,
  `bookid` int(6) UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`orderid`, `bookid`) USING BTREE,
  INDEX `fkIdx_87`(`orderid`) USING BTREE,
  INDEX `fkIdx_90`(`bookid`) USING BTREE,
  CONSTRAINT `FK_87` FOREIGN KEY (`orderid`) REFERENCES `orders` (`orderid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_90` FOREIGN KEY (`bookid`) REFERENCES `books` (`bookid`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for province
-- ----------------------------
DROP TABLE IF EXISTS `province`;
CREATE TABLE `province`  (
  `id` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `usersid` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `isadmin` tinyint NOT NULL DEFAULT 0,
  `isdisable` tinyint NOT NULL DEFAULT 0,
  `fullname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `male` tinyint NOT NULL DEFAULT 1,
  `addressid` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `addresstext` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `dob` date NULL DEFAULT NULL,
  PRIMARY KEY (`usersid`) USING BTREE,
  INDEX `fk_users_ward_1`(`addressid`) USING BTREE,
  CONSTRAINT `fk_users_ward_1` FOREIGN KEY (`addressid`) REFERENCES `ward` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for ward
-- ----------------------------
DROP TABLE IF EXISTS `ward`;
CREATE TABLE `ward`  (
  `id` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `did` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `did`(`did`) USING BTREE,
  CONSTRAINT `ward_ibfk_1` FOREIGN KEY (`did`) REFERENCES `district` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
