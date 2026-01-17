DROP DATABASE IF EXISTS `meowtea_schema`;
CREATE DATABASE `meowtea_schema` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `meowtea_schema`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Bảng ROLE
DROP TABLE IF EXISTS `Role`;
CREATE TABLE `Role` (
    `MaRole` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã vai trò
    `TenRole` VARCHAR(50) NOT NULL -- user / member / admin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Bảng USER
DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
    `MaUser` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã người dùng
    `Username` VARCHAR(100) NOT NULL UNIQUE, -- Tên đăng nhập
    `Password` VARCHAR(255) NOT NULL, -- Mật khẩu đã mã hóa
    `Ho` VARCHAR(50) NOT NULL, -- Họ người dùng
    `Ten` VARCHAR(50) NOT NULL, -- Tên người dùng
    `GioiTinh` ENUM('M', 'F', 'O') DEFAULT NULL, -- Giới tính: M=Nam, F=Nữ, O=Khác
    `DienThoai` VARCHAR(20), -- Số điện thoại
    `Email` VARCHAR(100), -- Email
    `TrangThai` TINYINT(1) DEFAULT 1, -- 1: Active, 0: Inactive
    `MaRole` INT NOT NULL, -- FK: Vai trò
    `DiaChi` TEXT DEFAULT NULL -- Địa chỉ người dùng
    CONSTRAINT `FK_User_Role` FOREIGN KEY (`MaRole`) REFERENCES `Role` (`MaRole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Bảng STORE
DROP TABLE IF EXISTS `Store`;
CREATE TABLE `Store` (
    `MaStore` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã cửa hàng
    `TenStore` VARCHAR(200) NOT NULL, -- Tên cửa hàng
    `DiaChi` TEXT NOT NULL, -- Địa chỉ
    `DienThoai` VARCHAR(20), -- Số điện thoại
    `TrangThai` TINYINT(1) DEFAULT 1 -- Trạng thái cửa hàng
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Bảng USER_STORE (N-N)
DROP TABLE IF EXISTS `User_Store`;
CREATE TABLE `User_Store` (
    `MaUser` INT NOT NULL,
    `MaStore` INT NOT NULL,
    PRIMARY KEY (`MaUser`, `MaStore`),
    CONSTRAINT `FK_US_User` FOREIGN KEY (`MaUser`) REFERENCES `User` (`MaUser`),
    CONSTRAINT `FK_US_Store` FOREIGN KEY (`MaStore`) REFERENCES `Store` (`MaStore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Bảng CATEGORY
DROP TABLE IF EXISTS `Category`;
CREATE TABLE `Category` (
    `MaCategory` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã danh mục
    `TenCategory` VARCHAR(100) NOT NULL, -- Tên danh mục
    `TrangThai` TINYINT(1) DEFAULT 1 -- Trạng thái
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Bảng SANPHAM
DROP TABLE IF EXISTS `SanPham`;
CREATE TABLE `SanPham` (
    `MaSP` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã sản phẩm
    `TenSP` VARCHAR(200) NOT NULL, -- Tên sản phẩm
    `GiaCoBan` DECIMAL(15, 0) NOT NULL DEFAULT 0, -- Giá bán cơ bản
    `HinhAnh` VARCHAR(255), -- URL hình ảnh
    `TrangThai` TINYINT(1) DEFAULT 1, -- Trạng thái
    `Rating` DECIMAL(3, 2) DEFAULT NULL, -- Đánh giá từ 1.00 đến 5.00
    `SoLuotRating` INT DEFAULT 0, -- Số lượt đánh giá
    `MaCategory` INT NOT NULL, -- FK: Danh mục
    CONSTRAINT `FK_SP_Category` FOREIGN KEY (`MaCategory`) REFERENCES `Category` (`MaCategory`),
    CONSTRAINT `CHK_Rating_Range` CHECK (`Rating` IS NULL OR (`Rating` >= 1.00 AND `Rating` <= 5.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Bảng OPTION_GROUP
DROP TABLE IF EXISTS `Option_Group`;
CREATE TABLE `Option_Group` (
    `MaOptionGroup` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã nhóm tùy chọn
    `TenNhom` VARCHAR(100) NOT NULL, -- Tên nhóm (đá, đường...)
    `IsMultiple` TINYINT(1) DEFAULT 0 -- 0: Chọn 1, 1: Chọn nhiều
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Bảng OPTION_VALUE
DROP TABLE IF EXISTS `Option_Value`;
CREATE TABLE `Option_Value` (
    `MaOptionValue` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã giá trị tùy chọn
    `TenGiaTri` VARCHAR(100) NOT NULL, -- Tên giá trị
    `GiaThem` DECIMAL(15, 0) DEFAULT 0, -- Giá cộng thêm
    `HinhAnh` VARCHAR(255) DEFAULT NULL, -- URL hình ảnh (chủ yếu cho topping)
    `MaOptionGroup` INT NOT NULL, -- FK: Nhóm tùy chọn
    CONSTRAINT `FK_OV_Group` FOREIGN KEY (`MaOptionGroup`) REFERENCES `Option_Group` (`MaOptionGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Bảng PRODUCT_OPTION_GROUP (N-N)
DROP TABLE IF EXISTS `Product_Option_Group`;
CREATE TABLE `Product_Option_Group` (
    `MaSP` INT NOT NULL, -- PK: Mã sản phẩm
    `MaOptionGroup` INT NOT NULL,
    PRIMARY KEY (`MaSP`, `MaOptionGroup`),
    CONSTRAINT `FK_POG_SP` FOREIGN KEY (`MaSP`) REFERENCES `SanPham` (`MaSP`),
    CONSTRAINT `FK_POG_Group` FOREIGN KEY (`MaOptionGroup`) REFERENCES `Option_Group` (`MaOptionGroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Bảng CART
DROP TABLE IF EXISTS `Cart`;
CREATE TABLE `Cart` (
    `MaCart` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã giỏ hàng
    `MaUser` INT NOT NULL, -- FK: Người sở hữu
    `MaStore` INT NOT NULL, -- FK: Cửa hàng
    `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP, -- Ngày tạo
    CONSTRAINT `FK_Cart_User` FOREIGN KEY (`MaUser`) REFERENCES `User` (`MaUser`),
    CONSTRAINT `FK_Cart_Store` FOREIGN KEY (`MaStore`) REFERENCES `Store` (`MaStore`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Bảng CART_ITEM
DROP TABLE IF EXISTS `Cart_Item`;
CREATE TABLE `Cart_Item` (
    `MaCartItem` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Chi tiết giỏ
    `MaCart` INT NOT NULL,
    `MaSP` INT NOT NULL,
    `SoLuong` INT DEFAULT 1,
    `GiaCoBan` DECIMAL(15, 0) NOT NULL, -- Giá tại thời điểm thêm
    CONSTRAINT `FK_CI_Cart` FOREIGN KEY (`MaCart`) REFERENCES `Cart` (`MaCart`) ON DELETE CASCADE,
    CONSTRAINT `FK_CI_SP` FOREIGN KEY (`MaSP`) REFERENCES `SanPham` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Bảng CART_ITEM_OPTION
DROP TABLE IF EXISTS `Cart_Item_Option`;
CREATE TABLE `Cart_Item_Option` (
    `MaCartItem` INT NOT NULL,
    `MaOptionValue` INT NOT NULL,
    `GiaThem` DECIMAL(15, 0) DEFAULT 0,
    PRIMARY KEY (`MaCartItem`, `MaOptionValue`),
    CONSTRAINT `FK_CIO_Item` FOREIGN KEY (`MaCartItem`) REFERENCES `Cart_Item` (`MaCartItem`) ON DELETE CASCADE,
    CONSTRAINT `FK_CIO_Value` FOREIGN KEY (`MaOptionValue`) REFERENCES `Option_Value` (`MaOptionValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Bảng ORDERS (Đổi tên từ ORDER để tránh từ khóa)
DROP TABLE IF EXISTS `Orders`;
CREATE TABLE `Orders` (
    `MaOrder` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã đơn hàng
    `MaUser` INT NOT NULL, -- FK: Khách đặt
    `MaStore` INT NOT NULL, -- FK: Cửa hàng xử lý
    `DiaChiGiao` TEXT NOT NULL, -- Địa chỉ giao hàng
    `PhiVanChuyen` DECIMAL(15, 0) DEFAULT 0,
    `MaPromotion` INT DEFAULT NULL, -- FK: Mã khuyến mãi
    `GiamGia` DECIMAL(15, 0) DEFAULT 0, -- Số tiền giảm giá
    `TongTien` DECIMAL(15, 0) NOT NULL,
    `TrangThai` VARCHAR(50) DEFAULT 'Pending', -- Trạng thái đơn
    `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `FK_Order_User` FOREIGN KEY (`MaUser`) REFERENCES `User` (`MaUser`),
    CONSTRAINT `FK_Order_Store` FOREIGN KEY (`MaStore`) REFERENCES `Store` (`MaStore`),
    CONSTRAINT `FK_Order_Promotion` FOREIGN KEY (`MaPromotion`) REFERENCES `Promotion` (`MaPromotion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Bảng ORDER_ITEM
DROP TABLE IF EXISTS `Order_Item`;
CREATE TABLE `Order_Item` (
    `MaOrderItem` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Chi tiết đơn
    `MaOrder` INT NOT NULL,
    `MaSP` INT NOT NULL,
    `SoLuong` INT DEFAULT 1,
    `GiaCoBan` DECIMAL(15, 0) NOT NULL,
    CONSTRAINT `FK_OI_Order` FOREIGN KEY (`MaOrder`) REFERENCES `Orders` (`MaOrder`) ON DELETE CASCADE,
    CONSTRAINT `FK_OI_SP` FOREIGN KEY (`MaSP`) REFERENCES `SanPham` (`MaSP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Bảng ORDER_ITEM_OPTION
DROP TABLE IF EXISTS `Order_Item_Option`;
CREATE TABLE `Order_Item_Option` (
    `MaOrderItem` INT NOT NULL,
    `MaOptionValue` INT NOT NULL,
    `GiaThem` DECIMAL(15, 0) DEFAULT 0,
    PRIMARY KEY (`MaOrderItem`, `MaOptionValue`),
    CONSTRAINT `FK_OIO_Item` FOREIGN KEY (`MaOrderItem`) REFERENCES `Order_Item` (`MaOrderItem`) ON DELETE CASCADE,
    CONSTRAINT `FK_OIO_Value` FOREIGN KEY (`MaOptionValue`) REFERENCES `Option_Value` (`MaOptionValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Bảng PAYMENT_METHOD
DROP TABLE IF EXISTS `Payment_Method`;
CREATE TABLE `Payment_Method` (
    `MaPayment` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã phương thức thanh toán
    `TenPayment` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 17. Bảng PROMOTION
DROP TABLE IF EXISTS `Promotion`;
CREATE TABLE `Promotion` (
    `MaPromotion` INT AUTO_INCREMENT PRIMARY KEY, -- PK: Mã khuyến mãi
    `Code` VARCHAR(50) NOT NULL UNIQUE,
    `LoaiGiamGia` VARCHAR(50), -- Percentage / Fixed
    `GiaTri` DECIMAL(15, 0) NOT NULL,
    `GiaTriToiDa` DECIMAL(15, 0) DEFAULT NULL, -- Giá trị tối đa cho khuyến mãi phần trăm (NULL nếu không giới hạn)
    `NgayBatDau` DATETIME,
    `NgayKetThuc` DATETIME,
    `TrangThai` TINYINT(1) DEFAULT 1 -- 1: Active, 0: Inactive
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 18. Bảng NEWS
DROP TABLE IF EXISTS `News`;
CREATE TABLE `News` (
    `MaNews` INT AUTO_INCREMENT PRIMARY KEY,
    `TieuDe` VARCHAR(255) NOT NULL,
    `NoiDung` VARCHAR(255) NOT NULL, -- Đường dẫn tới file markdown: assets/md/news/{MaNews}.md
    `HinhAnh` VARCHAR(255),
    `TrangThai` TINYINT(1) DEFAULT 1,
    `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;