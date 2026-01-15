/* * SEED DATA FOR BEVERAGE SHOP
 */

-- 1. Insert Role
INSERT INTO Role (TenRole) VALUES 
    (N'Admin'), 
    (N'Staff'), 
    (N'Customer');

-- 2. Insert Store (12 cửa hàng trải dài khắp Việt Nam)
INSERT INTO Store (TenStore, DiaChi, DienThoai, TrangThai) VALUES
-- Hồ Chí Minh (5 cửa hàng)
    (N'Đồng Khởi', N'91 Đồng Khởi, Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', N'033492824', 1),
    (N'Cộng Hòa', N'123 Cộng Hòa, Phường 12, Tân Bình, Thành Phố Hồ Chí Minh', N'033492825', 1),
    (N'Điện Biên Phủ', N'456 Điện Biên Phủ, Phường 25, Bình Thạnh, Thành Phố Hồ Chí Minh', N'033492826', 1),
    (N'Nguyễn Huệ', N'789 Nguyễn Huệ, Bến Nghé, Quận 1, Thành Phố Hồ Chí Minh', N'033492827', 1),
    (N'Lê Văn Việt', N'321 Lê Văn Việt, Hiệp Phú, Quận 9, Thành Phố Hồ Chí Minh', N'033492828', 1),
    -- Hà Nội (4 cửa hàng)
    (N'Cầu Giấy', N'45 Cầu Giấy, Quận Cầu Giấy, Hà Nội', N'033492829', 1),
    (N'Hoàn Kiếm', N'67 Phố Hàng Bông, Hoàn Kiếm, Hà Nội', N'033492830', 1),
    (N'Ba Đình', N'89 Nguyễn Trãi, Nguyễn Trung Trực, Ba Đình, Hà Nội', N'033492831', 1),
    (N'Đống Đa', N'234 Tây Sơn, Trung Liệt, Đống Đa, Hà Nội', N'033492832', 1),
    -- Cần Thơ (2 cửa hàng)
    (N'Ninh Kiều', N'123 Trần Hưng Đạo, Tân An, Ninh Kiều, Cần Thơ', N'033492833', 1),
    (N'Cái Răng', N'456 Nguyễn Văn Cừ, Lê Bình, Cái Răng, Cần Thơ', N'033492834', 1),
    -- Đà Nẵng (1 cửa hàng)
    (N'Hải Châu', N'789 Trần Phú, Hải Châu 1, Hải Châu, Đà Nẵng', N'033492835', 1);

-- 3. Insert User (Password giả định là plain text cho demo, thực tế cần hash)
INSERT INTO [User] (Username, [Password], Ho, Ten, GioiTinh, DienThoai, Email, MaRole) VALUES
    (N'admin', N'admin', N'Nguyễn', N'Quản Lý', 'M', N'0912345678', N'admin@shop.com', 1),
    (N'staff', N'staff', N'Trần', N'Nhân Viên', 'F', N'0987654321', N'staff1@shop.com', 2),
    (N'customer', N'cust', N'Lê', N'Khách Hàng', 'M', N'0911223344', N'customer@gmail.com', 3);

-- Phân công nhân viên vào cửa hàng
INSERT INTO User_Store (MaUser, MaStore) VALUES (2, 1);

-- 4. Insert Category
INSERT INTO Category (TenCategory) VALUES 
    (N'Cà phê truyền thống'), 
    (N'Trà sữa'), 
    (N'Trà trái cây'),
    (N'Yogurt');

-- 5. Insert Option Group
INSERT INTO Option_Group (TenNhom, IsMultiple) VALUES 
    (N'Mức đường', 0), -- Chọn 1
    (N'Mức đá', 0),    -- Chọn 1
    (N'Topping', 1);   -- Chọn nhiều

-- 6. Insert Option Value
-- Mức đường (Group 1)
INSERT INTO Option_Value (TenGiaTri, GiaThem, MaOptionGroup) VALUES
    (N'100% Đường', 0, 1),
    (N'70% Đường', 0, 1),
    (N'50% Đường', 0, 1),
    (N'Không đường', 0, 1);

-- Mức đá (Group 2)
INSERT INTO Option_Value (TenGiaTri, GiaThem, MaOptionGroup) VALUES
    (N'100% Đá', 0, 2),
    (N'50% Đá', 0, 2),
    (N'Không đá', 0, 2);

-- Topping (Group 3)
INSERT INTO Option_Value (TenGiaTri, GiaThem, HinhAnh, MaOptionGroup) VALUES
    (N'Trân châu đen', 5000, N'assets/img/products/topping/topping-tranchau.png', 3),
    (N'Thạch dừa', 5000, N'assets/img/products/topping/topping-thachdua.png', 3),
    (N'Pudding trứng', 10000, N'assets/img/products/topping/topping-pudding.png', 3),
    (N'Sương sáo', 5000, N'assets/img/products/topping/topping-suongsao.png', 3),
    (N'Cù năng', 5000, N'assets/img/products/topping/toppingcunang.png', 3);

-- 7. Insert SanPham
-- Cà phê truyền thống (MaCategory = 1)
INSERT INTO SanPham (TenSP, GiaCoBan, HinhAnh, Rating, SoLuotRating, MaCategory) VALUES
    (N'Cà phê Cappuccino', 35000, N'assets/img/products/caphe/caphe-cappucchino.png', 4.65, 245, 1),
    (N'Cà phê đen truyền thống', 25000, N'assets/img/products/caphe/caphe-dentruyenthong.png', 4.50, 328, 1),
    (N'Cà phê muối', 32000, N'assets/img/products/caphe/caphe-muoi.png', 4.75, 189, 1),
    (N'Cà phê sữa đá', 29000, N'assets/img/products/caphe/caphe-suada.png', 4.80, 456, 1);

-- Trà sữa (MaCategory = 2)
INSERT INTO SanPham (TenSP, GiaCoBan, HinhAnh, Rating, SoLuotRating, MaCategory) VALUES
    (N'Trà sữa dâu tây', 40000, N'assets/img/products/trasua/trasua-dautay.png', 4.85, 512, 2),
    (N'Trà sữa flan', 42000, N'assets/img/products/trasua/trasua-flan.png', 4.90, 645, 2),
    (N'Trà sữa Matcha', 40000, N'assets/img/products/trasua/trasua-mathca.png', 4.70, 432, 2),
    (N'Trà sữa socola', 38000, N'assets/img/products/trasua/trasua-socola.png', 4.65, 298, 2),
    (N'Trà sữa thái xanh', 39000, N'assets/img/products/trasua/trasua-thaixanh.png', 4.75, 356, 2),
    (N'Trà sữa việt quất', 40000, N'assets/img/products/trasua/trasua-vietquat.png', 4.80, 421, 2);

-- Trà trái cây (MaCategory = 3)
INSERT INTO SanPham (TenSP, GiaCoBan, HinhAnh, Rating, SoLuotRating, MaCategory) VALUES
    (N'Trà trái cây đào', 35000, N'assets/img/products/tratraicay/tratc-dao.png', 4.60, 234, 3),
    (N'Trà trái cây khóm', 35000, N'assets/img/products/tratraicay/tratc-khom.png', 4.55, 198, 3),
    (N'Trà trái cây sen vàng', 36000, N'assets/img/products/tratraicay/tratc-senvang.png', 4.70, 267, 3),
    (N'Trà trái cây vải', 35000, N'assets/img/products/tratraicay/tratc-vai.png', 4.65, 189, 3);

-- Yogurt (MaCategory = 4)
INSERT INTO SanPham (TenSP, GiaCoBan, HinhAnh, Rating, SoLuotRating, MaCategory) VALUES
    (N'Yogurt truyền thống', 38000, N'assets/img/products/yogurt/truyenthong.png', 4.75, 312, 4),
    (N'Yogurt dâu tây', 40000, N'assets/img/products/yogurt/dautay.png', 4.80, 278, 4);

-- 8. Link SanPham với Option Group (Product_Option_Group)
-- Cà phê (SP 1-4): Đường (1) và Đá (2)
INSERT INTO Product_Option_Group (MaSP, MaOptionGroup) VALUES 
    (1, 1), (1, 2),  -- Cà phê Cappuccino
    (2, 1), (2, 2),  -- Cà phê đen truyền thống
    (3, 1), (3, 2),  -- Cà phê muối
    (4, 1), (4, 2);  -- Cà phê sữa đá

-- Trà sữa (SP 5-10): Đường (1), Đá (2), Topping (3)
INSERT INTO Product_Option_Group (MaSP, MaOptionGroup) VALUES 
    (5, 1), (5, 2), (5, 3),  -- Trà sữa dâu tây
    (6, 1), (6, 2), (6, 3),  -- Trà sữa flan
    (7, 1), (7, 2), (7, 3),  -- Trà sữa Matcha
    (8, 1), (8, 2), (8, 3),  -- Trà sữa socola
    (9, 1), (9, 2), (9, 3),  -- Trà sữa thái xanh
    (10, 1), (10, 2), (10, 3);  -- Trà sữa việt quất

-- Trà trái cây (SP 11-14): Đường (1), Đá (2)
INSERT INTO Product_Option_Group (MaSP, MaOptionGroup) VALUES 
    (11, 1), (11, 2),  -- Trà trái cây đào
    (12, 1), (12, 2),  -- Trà trái cây khóm
    (13, 1), (13, 2),  -- Trà trái cây sen vàng
    (14, 1), (14, 2);  -- Trà trái cây vải

-- Yogurt (SP 15-16): Đường (1), Đá (2), Topping (3)
INSERT INTO Product_Option_Group (MaSP, MaOptionGroup) VALUES 
    (15, 1), (15, 2), (15, 3),  -- Yogurt truyền thống
    (16, 1), (16, 2), (16, 3);  -- Yogurt dâu tây

-- 9. Insert Payment Method
INSERT INTO Payment_Method (TenPayment) VALUES (N'Tiền mặt'), (N'Chuyển khoản'), (N'Momo');

-- 10. Sample Order Data (Mô phỏng 1 đơn hàng)
-- Khách hàng (User 3) đặt tại Cửa hàng 1
INSERT INTO Orders (MaUser, MaStore, DiaChiGiao, TongTien, TrangThai) 
VALUES (3, 1, N'Nhà số 5, Đường ABC', 50000, N'Completed');

-- Chi tiết đơn: 1 ly Trà sữa dâu tây (40k)
SET IDENTITY_INSERT Order_Item ON;
INSERT INTO Order_Item (MaOrderItem, MaOrder, MaSP, SoLuong, GiaCoBan) 
VALUES (1, 1, 5, 1, 40000);
SET IDENTITY_INSERT Order_Item OFF;

-- Option cho ly trà sữa đó: 50% Đường, 100% Đá, thêm Pudding (10k)
-- Tổng ly này = 40k + 10k = 50k
INSERT INTO Order_Item_Option (MaOrderItem, MaOptionValue, GiaThem) VALUES 
    (1, 3, 0),   -- 50% Đường
    (1, 5, 0),   -- 100% Đá
    (1, 8, 10000); -- Pudding trứng

-- 12. Insert News
-- NoiDung lưu đường dẫn tới file markdown: assets/md/news/{MaNews}.md
INSERT INTO News (TieuDe, NoiDung, HinhAnh, TrangThai, NgayTao) VALUES
    (N'Những lợi ích tuyệt vời của nước ép trái cây đối với sức khỏe', 
     N'assets/md/news/1.md',
     N'assets/img/news/news_one.jpg', 1, '2024-12-24T10:00:00'),
    (N'Cà Phê Cappuccino Dừa lần đầu tiên có mặt tại MeowTea Fresh',
     N'assets/md/news/2.md',
     N'assets/img/news/news_two.jpg', 1, '2024-12-05T10:00:00'),
    (N'MeowTea Fresh ra mắt dòng sản phẩm Matcha - dấu ấn độc đáo',
     N'assets/md/news/3.md',
     N'assets/img/news/news_three.png', 1, '2024-12-09T10:00:00'),
    (N'App Thành Viên MeowTea Fresh chính thức ra mắt trên Android & iOS',
     N'assets/md/news/4.md',
     N'assets/img/news/news_banner.jpg', 1, '2024-12-15T10:00:00');