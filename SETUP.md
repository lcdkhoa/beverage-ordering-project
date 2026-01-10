# Hướng Dẫn Setup MeowTea Fresh Website

## Yêu Cầu
- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 trở lên
- MySQL 5.7 trở lên

## Các Bước Setup

### 1. Cấu Hình Database

1. Mở XAMPP Control Panel và khởi động Apache và MySQL
2. Mở phpMyAdmin (http://localhost/phpmyadmin)
3. Import database schema:
   - Chọn tab "Import"
   - Chọn file `database/schema.sql`
   - Click "Go" để import
4. Import seed data:
   - Chọn tab "Import" 
   - Chọn file `database/seed-data.sql`
   - Click "Go" để import

### 2. Cấu Hình Database Connection

File `database/config.php` đã được cấu hình mặc định cho XAMPP:
- Host: localhost
- User: root
- Password: (trống)
- Database: meowtea_schema

Nếu bạn có cấu hình khác, hãy sửa trong file `database/config.php`.

### 3. Kiểm Tra Cấu Trúc Thư Mục

Đảm bảo các thư mục sau tồn tại:
```
projects_web_php/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── img/
├── components/
│   ├── header.php
│   ├── footer.php
│   ├── product-card.php
│   ├── news-card.php
│   └── button.php
├── database/
│   ├── config.php
│   ├── schema.sql
│   └── seed-data.sql
├── functions.php
└── index.php
```

### 4. Truy Cập Website

1. Đặt toàn bộ project vào thư mục `htdocs` của XAMPP:
   - Windows: `C:\xampp\htdocs\projects_web_php\`
   - Hoặc tạo virtual host nếu muốn

2. Truy cập website:
   - http://localhost/projects_web_php/
   - Hoặc http://localhost/ (nếu đặt ở root)

## Cấu Trúc Database

Database `meowtea_schema` chứa các bảng:
- Role, User, Store
- Category, SanPham
- Option_Group, Option_Value
- Cart, Cart_Item, Cart_Item_Option
- Orders, Order_Item, Order_Item_Option
- Payment_Method, Promotion, News

## Seed Data

Sau khi import seed data, bạn sẽ có:
- 3 roles: Admin, Staff, Customer
- 2 stores
- 3 users (admin, staff, customer)
- 3 categories: Cà phê, Trà sữa, Đá xay
- 8 sản phẩm
- 3 tin tức
- Options cho sản phẩm (đường, đá, topping)

## Lưu Ý

- Đảm bảo các hình ảnh trong `assets/img/` đã được đặt đúng vị trí
- Nếu có lỗi kết nối database, kiểm tra lại cấu hình trong `database/config.php`
- Password trong seed data là plain text (chỉ dùng cho demo)

## Tiếp Theo

Các trang cần implement tiếp theo:
- `pages/menu/index.php` - Trang menu sản phẩm
- `pages/stores/index.php` - Trang danh sách cửa hàng
- `pages/news/index.php` - Trang tin tức
- `pages/about/index.php` - Trang giới thiệu
- `pages/cart/index.php` - Trang giỏ hàng
- `pages/auth/login.php` - Trang đăng nhập
