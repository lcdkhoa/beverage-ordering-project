IF DB_ID('meowtea_schema') IS NOT NULL
BEGIN
    ALTER DATABASE meowtea_schema SET SINGLE_USER WITH ROLLBACK IMMEDIATE;
    DROP DATABASE meowtea_schema;
END;
GO

CREATE DATABASE meowtea_schema;
GO

USE meowtea_schema;
GO

-- 1. Bảng ROLE
IF OBJECT_ID('Role', 'U') IS NOT NULL DROP TABLE Role;
GO
CREATE TABLE Role (
    MaRole INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã vai trò
    TenRole NVARCHAR(50) NOT NULL -- user / member / admin
);
GO

-- 2. Bảng USER
IF OBJECT_ID('User', 'U') IS NOT NULL DROP TABLE [User];
GO
CREATE TABLE [User] (
    MaUser INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã người dùng
    Username NVARCHAR(100) NOT NULL UNIQUE, -- Tên đăng nhập
    [Password] NVARCHAR(255) NOT NULL, -- Mật khẩu đã mã hóa
    Ho NVARCHAR(50) NOT NULL, -- Họ người dùng
    Ten NVARCHAR(50) NOT NULL, -- Tên người dùng
    GioiTinh CHAR(1) NULL CHECK (GioiTinh IN ('M','F','O')), -- Giới tính
    DienThoai NVARCHAR(20), -- Số điện thoại
    Email NVARCHAR(100), -- Email
    TrangThai BIT DEFAULT 1, -- 1: Active, 0: Inactive
    MaRole INT NOT NULL, -- FK: Vai trò
    CONSTRAINT FK_User_Role FOREIGN KEY (MaRole) REFERENCES Role (MaRole)
);
GO

-- 3. Bảng STORE
IF OBJECT_ID('Store', 'U') IS NOT NULL DROP TABLE Store;
GO
CREATE TABLE Store (
    MaStore INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã cửa hàng
    TenStore NVARCHAR(200) NOT NULL, -- Tên cửa hàng
    DiaChi NVARCHAR(MAX) NOT NULL, -- Địa chỉ
    DienThoai NVARCHAR(20), -- Số điện thoại
    TrangThai BIT DEFAULT 1 -- Trạng thái cửa hàng
);
GO

-- 4. Bảng USER_STORE (N-N)
IF OBJECT_ID('User_Store', 'U') IS NOT NULL DROP TABLE User_Store;
GO
CREATE TABLE User_Store (
    MaUser INT NOT NULL,
    MaStore INT NOT NULL,
    PRIMARY KEY (MaUser, MaStore),
    CONSTRAINT FK_US_User FOREIGN KEY (MaUser) REFERENCES [User] (MaUser),
    CONSTRAINT FK_US_Store FOREIGN KEY (MaStore) REFERENCES Store (MaStore)
);
GO

-- 5. Bảng CATEGORY
IF OBJECT_ID('Category', 'U') IS NOT NULL DROP TABLE Category;
GO
CREATE TABLE Category (
    MaCategory INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã danh mục
    TenCategory NVARCHAR(100) NOT NULL, -- Tên danh mục
    TrangThai BIT DEFAULT 1 -- Trạng thái
);
GO

-- 6. Bảng SANPHAM
IF OBJECT_ID('SanPham', 'U') IS NOT NULL DROP TABLE SanPham;
GO
CREATE TABLE SanPham (
    MaSP INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã sản phẩm
    TenSP NVARCHAR(200) NOT NULL, -- Tên sản phẩm
    GiaCoBan DECIMAL(15, 0) NOT NULL DEFAULT 0, -- Giá bán cơ bản
    HinhAnh NVARCHAR(255), -- URL hình ảnh
    TrangThai BIT DEFAULT 1, -- Trạng thái
    Rating DECIMAL(3, 2) DEFAULT NULL, -- Đánh giá từ 1.00 đến 5.00
    SoLuotRating INT DEFAULT 0, -- Số lượt đánh giá
    MaCategory INT NOT NULL, -- FK: Danh mục
    CONSTRAINT FK_SP_Category FOREIGN KEY (MaCategory) REFERENCES Category (MaCategory),
    CONSTRAINT CHK_Rating_Range CHECK (Rating IS NULL OR (Rating >= 1.00 AND Rating <= 5.00))
);
GO

-- 7. Bảng OPTION_GROUP
IF OBJECT_ID('Option_Group', 'U') IS NOT NULL DROP TABLE Option_Group;
GO
CREATE TABLE Option_Group (
    MaOptionGroup INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã nhóm tùy chọn
    TenNhom NVARCHAR(100) NOT NULL, -- Tên nhóm (đá, đường...)
    IsMultiple BIT DEFAULT 0 -- 0: Chọn 1, 1: Chọn nhiều
);
GO

-- 8. Bảng OPTION_VALUE
IF OBJECT_ID('Option_Value', 'U') IS NOT NULL DROP TABLE Option_Value;
GO
CREATE TABLE Option_Value (
    MaOptionValue INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã giá trị tùy chọn
    TenGiaTri NVARCHAR(100) NOT NULL, -- Tên giá trị
    GiaThem DECIMAL(15, 0) DEFAULT 0, -- Giá cộng thêm
    HinhAnh NVARCHAR(255) DEFAULT NULL, -- URL hình ảnh (chủ yếu cho topping)
    MaOptionGroup INT NOT NULL, -- FK: Nhóm tùy chọn
    CONSTRAINT FK_OV_Group FOREIGN KEY (MaOptionGroup) REFERENCES Option_Group (MaOptionGroup)
);
GO

-- 9. Bảng PRODUCT_OPTION_GROUP (N-N)
IF OBJECT_ID('Product_Option_Group', 'U') IS NOT NULL DROP TABLE Product_Option_Group;
GO
CREATE TABLE Product_Option_Group (
    MaSP INT NOT NULL, -- PK: Mã sản phẩm
    MaOptionGroup INT NOT NULL,
    PRIMARY KEY (MaSP, MaOptionGroup),
    CONSTRAINT FK_POG_SP FOREIGN KEY (MaSP) REFERENCES SanPham (MaSP),
    CONSTRAINT FK_POG_Group FOREIGN KEY (MaOptionGroup) REFERENCES Option_Group (MaOptionGroup)
);
GO

-- 10. Bảng CART
IF OBJECT_ID('Cart', 'U') IS NOT NULL DROP TABLE Cart;
GO
CREATE TABLE Cart (
    MaCart INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã giỏ hàng
    MaUser INT NOT NULL, -- FK: Người sở hữu
    MaStore INT NOT NULL, -- FK: Cửa hàng
    NgayTao DATETIME2 DEFAULT SYSDATETIME(), -- Ngày tạo
    CONSTRAINT FK_Cart_User FOREIGN KEY (MaUser) REFERENCES [User] (MaUser),
    CONSTRAINT FK_Cart_Store FOREIGN KEY (MaStore) REFERENCES Store (MaStore)
);
GO

-- 11. Bảng CART_ITEM
IF OBJECT_ID('Cart_Item', 'U') IS NOT NULL DROP TABLE Cart_Item;
GO
CREATE TABLE Cart_Item (
    MaCartItem INT IDENTITY(1,1) PRIMARY KEY, -- PK: Chi tiết giỏ
    MaCart INT NOT NULL,
    MaSP INT NOT NULL,
    SoLuong INT DEFAULT 1,
    GiaCoBan DECIMAL(15, 0) NOT NULL, -- Giá tại thời điểm thêm
    CONSTRAINT FK_CI_Cart FOREIGN KEY (MaCart) REFERENCES Cart (MaCart) ON DELETE CASCADE,
    CONSTRAINT FK_CI_SP FOREIGN KEY (MaSP) REFERENCES SanPham (MaSP)
);
GO

-- 12. Bảng CART_ITEM_OPTION
IF OBJECT_ID('Cart_Item_Option', 'U') IS NOT NULL DROP TABLE Cart_Item_Option;
GO
CREATE TABLE Cart_Item_Option (
    MaCartItem INT NOT NULL,
    MaOptionValue INT NOT NULL,
    GiaThem DECIMAL(15, 0) DEFAULT 0,
    PRIMARY KEY (MaCartItem, MaOptionValue),
    CONSTRAINT FK_CIO_Item FOREIGN KEY (MaCartItem) REFERENCES Cart_Item (MaCartItem) ON DELETE CASCADE,
    CONSTRAINT FK_CIO_Value FOREIGN KEY (MaOptionValue) REFERENCES Option_Value (MaOptionValue)
);
GO

-- 13. Bảng ORDERS
IF OBJECT_ID('Orders', 'U') IS NOT NULL DROP TABLE Orders;
GO
CREATE TABLE Orders (
    MaOrder INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã đơn hàng
    MaUser INT NOT NULL, -- FK: Khách đặt
    MaStore INT NOT NULL, -- FK: Cửa hàng xử lý
    DiaChiGiao NVARCHAR(MAX) NOT NULL, -- Địa chỉ giao hàng
    PhiVanChuyen DECIMAL(15, 0) DEFAULT 0,
    MaPromotion INT DEFAULT NULL, -- FK: Mã khuyến mãi
    GiamGia DECIMAL(15, 0) DEFAULT 0, -- Số tiền giảm giá
    TongTien DECIMAL(15, 0) NOT NULL,
    TrangThai NVARCHAR(50) DEFAULT N'Pending', -- Trạng thái đơn
    NgayTao DATETIME2 DEFAULT SYSDATETIME(),
    CONSTRAINT FK_Order_User FOREIGN KEY (MaUser) REFERENCES [User] (MaUser),
    CONSTRAINT FK_Order_Store FOREIGN KEY (MaStore) REFERENCES Store (MaStore),
    CONSTRAINT FK_Order_Promotion FOREIGN KEY (MaPromotion) REFERENCES Promotion (MaPromotion)
);
GO

-- 14. Bảng ORDER_ITEM
IF OBJECT_ID('Order_Item', 'U') IS NOT NULL DROP TABLE Order_Item;
GO
CREATE TABLE Order_Item (
    MaOrderItem INT IDENTITY(1,1) PRIMARY KEY, -- PK: Chi tiết đơn
    MaOrder INT NOT NULL,
    MaSP INT NOT NULL,
    SoLuong INT DEFAULT 1,
    GiaCoBan DECIMAL(15, 0) NOT NULL,
    CONSTRAINT FK_OI_Order FOREIGN KEY (MaOrder) REFERENCES Orders (MaOrder) ON DELETE CASCADE,
    CONSTRAINT FK_OI_SP FOREIGN KEY (MaSP) REFERENCES SanPham (MaSP)
);
GO

-- 15. Bảng ORDER_ITEM_OPTION
IF OBJECT_ID('Order_Item_Option', 'U') IS NOT NULL DROP TABLE Order_Item_Option;
GO
CREATE TABLE Order_Item_Option (
    MaOrderItem INT NOT NULL,
    MaOptionValue INT NOT NULL,
    GiaThem DECIMAL(15, 0) DEFAULT 0,
    PRIMARY KEY (MaOrderItem, MaOptionValue),
    CONSTRAINT FK_OIO_Item FOREIGN KEY (MaOrderItem) REFERENCES Order_Item (MaOrderItem) ON DELETE CASCADE,
    CONSTRAINT FK_OIO_Value FOREIGN KEY (MaOptionValue) REFERENCES Option_Value (MaOptionValue)
);
GO

-- 16. Bảng PAYMENT_METHOD
IF OBJECT_ID('Payment_Method', 'U') IS NOT NULL DROP TABLE Payment_Method;
GO
CREATE TABLE Payment_Method (
    MaPayment INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã phương thức thanh toán
    TenPayment NVARCHAR(100) NOT NULL
);
GO

-- 17. Bảng PROMOTION
IF OBJECT_ID('Promotion', 'U') IS NOT NULL DROP TABLE Promotion;
GO
CREATE TABLE Promotion (
    MaPromotion INT IDENTITY(1,1) PRIMARY KEY, -- PK: Mã khuyến mãi
    Code NVARCHAR(50) NOT NULL UNIQUE,
    LoaiGiamGia NVARCHAR(50), -- Percentage / Fixed
    GiaTri DECIMAL(15, 0) NOT NULL,
    GiaTriToiDa DECIMAL(15, 0) DEFAULT NULL, -- Giá trị tối đa cho khuyến mãi phần trăm (NULL nếu không giới hạn)
    NgayBatDau DATETIME2,
    NgayKetThuc DATETIME2,
    TrangThai BIT DEFAULT 1 -- 1: Active, 0: Inactive
);
GO

-- 18. Bảng NEWS
IF OBJECT_ID('News', 'U') IS NOT NULL DROP TABLE News;
GO
CREATE TABLE News (
    MaNews INT IDENTITY(1,1) PRIMARY KEY,
    TieuDe NVARCHAR(255) NOT NULL,
    NoiDung NVARCHAR(255) NOT NULL, -- Đường dẫn tới file markdown: assets/md/news/{MaNews}.md
    HinhAnh NVARCHAR(255),
    TrangThai BIT DEFAULT 1,
    NgayTao DATETIME2 DEFAULT SYSDATETIME()
);
GO