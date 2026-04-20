SET NAMES utf8mb4;

INSERT IGNORE INTO roles (id, name, description) VALUES
('ROL_ADMIN', 'admin', 'System administrator'),
('ROL_MANAGER', 'manager', 'Business manager'),
('ROL_SALES', 'sales', 'Sales and support staff'),
('ROL_OPER', 'operations', 'Operations staff'),
('ROL_CUST', 'customer', 'Customer');

INSERT IGNORE INTO users (
  id, role_id, full_name, email, phone, password_hash, status, created_at, updated_at
) VALUES
('USR_ADMIN', 'ROL_ADMIN', 'ClearVision Admin', 'admin@clearvision.vn', '0900000001', '$2y$10$FhdfzAjeJPwpx5C27k357OBVvpBRLDrQucLYgUS9AxuLT19Q8njvm', 'active', NOW(), NOW()),
('USR_CUST1', 'ROL_CUST', 'Nguyen Van A', 'customer@clearvision.vn', '0900000002', '$2y$10$FhdfzAjeJPwpx5C27k357OBVvpBRLDrQucLYgUS9AxuLT19Q8njvm', 'active', NOW(), NOW());

INSERT IGNORE INTO addresses (
  id, user_id, receiver_name, receiver_phone, province, district, ward, address_line, is_default, created_at
) VALUES
('ADR_CUST1', 'USR_CUST1', 'Nguyen Van A', '0900000002', 'Ho Chi Minh', 'Quan 1', 'Ben Nghe', '123 Le Loi', 1, NOW());

INSERT IGNORE INTO categories (id, name, slug, description) VALUES
('CAT_FRAME', 'Gọng kính', 'gong-kinh', 'Các loại gọng kính'),
('CAT_LENS', 'Tròng kính', 'trong-kinh', 'Các loại tròng kính'),
('CAT_SUN', 'Kính râm', 'kinh-ram', 'Kính chống nắng'),
('CAT_ACC', 'Phụ kiện', 'phu-kien', 'Phụ kiện mắt kính');

INSERT IGNORE INTO brands (id, name, slug, country, description, is_active) VALUES
('BRD_RB', 'Ray-Ban', 'ray-ban', 'USA', 'Classic eyewear brand', 1),
('BRD_PD', 'Prada', 'prada', 'Italy', 'Luxury eyewear', 1),
('BRD_GC', 'Gucci', 'gucci', 'Italy', 'Fashion eyewear', 1);

INSERT IGNORE INTO products (
  id, category_id, brand_id, name, slug, product_type, description, is_active, created_at, updated_at
) VALUES
('PRD_001', 'CAT_FRAME', 'BRD_RB', 'Ray-Ban Clubmaster Classic', 'ray-ban-clubmaster-classic', 'ready_stock', 'Gọng kính kinh điển, nhẹ và dễ đeo hằng ngày.', 1, NOW(), NOW()),
('PRD_002', 'CAT_SUN', 'BRD_PD', 'Prada Heritage Oversized', 'prada-heritage-oversized', 'pre_order', 'Mẫu kính râm cao cấp, phù hợp thời trang công sở.', 1, NOW(), NOW()),
('PRD_003', 'CAT_LENS', 'BRD_GC', 'Blue Light Lens Premium', 'blue-light-lens-premium', 'prescription', 'Tròng kính chống ánh sáng xanh dành cho dân văn phòng.', 1, NOW(), NOW());

INSERT IGNORE INTO product_variants (
  id, product_id, sku, variant_name, frame_style, lens_type, color, size, material, price, stock_quantity, image_3d_url, is_active, created_at, updated_at
) VALUES
('VAR_001', 'PRD_001', 'RB-3016-W0365', 'Clubmaster Black Gold', 'Clubmaster', NULL, 'Đen', 'M', 'Acetate', 4250000, 10, NULL, 1, NOW(), NOW()),
('VAR_002', 'PRD_001', 'RB-3016-TORT', 'Clubmaster Tortoise', 'Clubmaster', NULL, 'Đồi mồi', 'M', 'Acetate', 4350000, 6, NULL, 1, NOW(), NOW()),
('VAR_003', 'PRD_002', 'PR-543-ST', 'Oversized Black', 'Oversized', NULL, 'Đen', 'L', 'Titanium', 6750000, 0, NULL, 1, NOW(), NOW()),
('VAR_004', 'PRD_003', 'GC-LENS-BL', 'Blue Light Lens', NULL, 'Blue Light', 'Trong', '1.56', 'Resin', 1250000, 20, NULL, 1, NOW(), NOW());

INSERT IGNORE INTO product_images (id, product_id, variant_id, image_url, image_type, sort_order) VALUES
('IMG_001', 'PRD_001', 'VAR_001', 'assets/images/about-us/eyewear-display.png', 'primary', 1),
('IMG_002', 'PRD_001', 'VAR_002', 'assets/images/product-detail/promo-glasses.png', 'primary', 2),
('IMG_003', 'PRD_002', 'VAR_003', 'assets/images/product-detail/promo-glasses.png', 'primary', 1),
('IMG_004', 'PRD_003', 'VAR_004', 'assets/images/warranty-policy/reference.png', 'primary', 1);

INSERT IGNORE INTO vouchers (
  id, name, code, discount_type, discount_value, min_order_value, max_discount_value, start_at, expired_at, usage_limit, is_active
) VALUES
('VCH_001', 'Khách mới', 'WELCOME20', 'percent', 20, 500000, 300000, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 100, 1);
