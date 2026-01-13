/* * SEED DATA FOR BEVERAGE SHOP
 */

-- 1. Insert Role
INSERT INTO `Role` (TenRole) VALUES 
('Admin'), 
('Staff'), 
('Customer');

-- 2. Insert Store (12 cửa hàng trải dài khắp Việt Nam)
INSERT INTO `Store` (TenStore, DiaChi, DienThoai, TrangThai) VALUES
-- Hồ Chí Minh (5 cửa hàng)
('Đồng Khởi', '91 Đồng Khởi, Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', '033492824', 1),
('Cộng Hòa', '123 Cộng Hòa, Phường 12, Tân Bình, Thành Phố Hồ Chí Minh', '033492825', 1),
('Điện Biên Phủ', '456 Điện Biên Phủ, Phường 25, Bình Thạnh, Thành Phố Hồ Chí Minh', '033492826', 1),
('Nguyễn Huệ', '789 Nguyễn Huệ, Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', '033492827', 1),
('Lê Văn Việt', '321 Lê Văn Việt, Hiệp Phú, Quận 9, Thành Phố Hồ Chí Minh', '033492828', 1),
-- Hà Nội (4 cửa hàng)
('Cầu Giấy', '45 Cầu Giấy, Quận Cầu Giấy, Hà Nội', '033492829', 1),
('Hoàn Kiếm', '67 Phố Hàng Bông, Hoàn Kiếm, Hà Nội', '033492830', 1),
('Ba Đình', '89 Nguyễn Trãi, Nguyễn Trung Trực, Ba Đình, Hà Nội', '033492831', 1),
('Đống Đa', '234 Tây Sơn, Trung Liệt, Đống Đa, Hà Nội', '033492832', 1),
-- Cần Thơ (2 cửa hàng)
('Ninh Kiều', '123 Trần Hưng Đạo, Tân An, Ninh Kiều, Cần Thơ', '033492833', 1),
('Cái Răng', '456 Nguyễn Văn Cừ, Lê Bình, Cái Răng, Cần Thơ', '033492834', 1),
-- Đà Nẵng (1 cửa hàng)
('Hải Châu', '789 Trần Phú, Hải Châu 1, Hải Châu, Đà Nẵng', '033492835', 1);

-- 3. Insert User (Password giả định là plain text cho demo, thực tế cần hash)
INSERT INTO `User` (Username, Password, Ho, Ten, GioiTinh, DienThoai, Email, MaRole) VALUES
('admin01', 'password123', 'Nguyễn', 'Quản Lý', 'M', '0912345678', 'admin@shop.com', 1),
('staff_q1', 'staff123', 'Trần', 'Nhân Viên', 'F', '0987654321', 'staff1@shop.com', 2),
('customer01', 'cust123', 'Lê', 'Khách Hàng', 'M', '0911223344', 'customer@gmail.com', 3);

-- Phân công nhân viên vào cửa hàng
INSERT INTO `User_Store` (MaUser, MaStore) VALUES (2, 1);

-- 4. Insert Category
INSERT INTO `Category` (TenCategory) VALUES 
('Cà phê truyền thống'), 
('Trà sữa'), 
('Trà trái cây');

-- 5. Insert Option Group
INSERT INTO `Option_Group` (TenNhom, IsMultiple) VALUES 
('Mức đường', 0), -- Chọn 1
('Mức đá', 0),    -- Chọn 1
('Topping', 1);   -- Chọn nhiều

-- 6. Insert Option Value
-- Mức đường (Group 1)
INSERT INTO `Option_Value` (TenGiaTri, GiaThem, MaOptionGroup) VALUES
('100% Đường', 0, 1),
('70% Đường', 0, 1),
('50% Đường', 0, 1),
('Không đường', 0, 1);

-- Mức đá (Group 2)
INSERT INTO `Option_Value` (TenGiaTri, GiaThem, MaOptionGroup) VALUES
('100% Đá', 0, 2),
('50% Đá', 0, 2),
('Không đá', 0, 2);

-- Topping (Group 3)
INSERT INTO `Option_Value` (TenGiaTri, GiaThem, MaOptionGroup) VALUES
('Trân châu đen', 5000, 3),
('Thạch dừa', 5000, 3),
('Pudding trứng', 10000, 3);

-- 7. Insert SanPham
INSERT INTO `SanPham` (TenSP, GiaCoBan, HinhAnh, Rating, SoLuotRating, MaCategory) VALUES
('Cà phê đen đá', 25000, 'assets/img/products/product_one.png', 4.50, 128, 1),
('Cà phê sữa đá', 29000, 'assets/img/products/product_two.png', 4.75, 256, 1),
('Trà sữa truyền thống', 35000, 'assets/img/products/product_three.jpg', 4.85, 512, 2),
('Trà sữa Matcha', 40000, 'assets/img/products/product_four.jpg', 4.60, 384, 2);

-- 8. Link SanPham với Option Group (Product_Option_Group)
-- Cà phê đen (SP 1) chỉ có Đường (1) và Đá (2)
INSERT INTO `Product_Option_Group` (MaSP, MaOptionGroup) VALUES (1, 1), (1, 2);
-- Cà phê sữa (SP 2) chỉ có Đường (1) và Đá (2)
INSERT INTO `Product_Option_Group` (MaSP, MaOptionGroup) VALUES (2, 1), (2, 2);
-- Trà sữa (SP 3) có Đường (1), Đá (2), Topping (3)
INSERT INTO `Product_Option_Group` (MaSP, MaOptionGroup) VALUES (3, 1), (3, 2), (3, 3);

-- 9. Insert Payment Method
INSERT INTO `Payment_Method` (TenPayment) VALUES ('Tiền mặt'), ('Chuyển khoản'), ('Momo');

-- 10. Sample Order Data (Mô phỏng 1 đơn hàng)
-- Khách hàng (User 3) đặt tại Cửa hàng 1
INSERT INTO `Orders` (MaUser, MaStore, DiaChiGiao, TongTien, TrangThai) 
VALUES (3, 1, 'Nhà số 5, Đường ABC', 45000, 'Completed');

-- Chi tiết đơn: 1 ly Trà sữa truyền thống (35k)
INSERT INTO `Order_Item` (MaOrderItem, MaOrder, MaSP, SoLuong, GiaCoBan) 
VALUES (1, 1, 3, 1, 35000);

-- Option cho ly trà sữa đó: 50% Đường, 100% Đá, thêm Pudding (10k)
-- Tổng ly này = 35k + 10k = 45k
INSERT INTO `Order_Item_Option` (MaOrderItem, MaOptionValue, GiaThem) VALUES 
(1, 3, 0),   -- 50% Đường
(1, 5, 0),   -- 100% Đá
(1, 10, 10000); -- Pudding trứng

-- 11. Insert thêm sản phẩm để có đủ best seller
INSERT INTO `SanPham` (TenSP, GiaCoBan, HinhAnh, Rating, SoLuotRating, MaCategory) VALUES
('Mê Dừa Non', 35000, 'assets/img/products/product_one.png', 4.90, 645, 2),
('Trà sữa Hồng D\'Ran', 35000, 'assets/img/products/product_two.png', 4.65, 298, 2),
('Nước ép Kiwi', 35000, 'assets/img/products/product_three.jpg', 4.20, 156, 3),
('Matcha Latte', 40000, 'assets/img/products/product_four.jpg', 4.55, 432, 2);

-- Link options cho các sản phẩm mới
INSERT INTO `Product_Option_Group` (MaSP, MaOptionGroup) VALUES 
(5, 1), (5, 2), (5, 3),  -- Mê Dừa Non
(6, 1), (6, 2), (6, 3),  -- Trà sữa Hồng D'Ran
(7, 1), (7, 2),           -- Nước ép Kiwi
(8, 1), (8, 2), (8, 3);   -- Matcha Latte

-- 12. Insert News
-- NoiDung lưu đường dẫn tới file markdown: assets/md/news/{MaNews}.md
INSERT INTO `News` (TieuDe, NoiDung, HinhAnh, TrangThai, NgayTao) VALUES
('Những lợi ích tuyệt vời của nước ép trái cây đối với sức khỏe', 
 'assets/md/news/1.md',
 'assets/img/news/news_one.jpg', 1, '2024-12-24 10:00:00'),
('Cà Phê Cappuccino Dừa lần đầu tiên có mặt tại MeowTea Fresh',
 'assets/md/news/2.md',
 'assets/img/news/news_two.jpg', 1, '2024-12-05 10:00:00'),
('MeowTea Fresh ra mắt dòng sản phẩm Matcha - dấu ấn độc đáo',
 'assets/md/news/3.md',
 'assets/img/news/news_three.png', 1, '2024-12-09 10:00:00'),
('App Thành Viên MeowTea Fresh chính thức ra mắt trên Android & iOS',
 'assets/md/news/4.md',
 'assets/img/news/news_banner.jpg', 1, '2024-12-15 10:00:00');