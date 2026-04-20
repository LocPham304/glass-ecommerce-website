SET NAMES utf8mb4;

CREATE TABLE `roles` (
  `id` char(20) PRIMARY KEY,
  `name` varchar(50) UNIQUE NOT NULL,
  `description` varchar(255)
);

CREATE TABLE `users` (
  `id` char(20) PRIMARY KEY,
  `role_id` char(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL,
  `phone` varchar(20),
  `password_hash` varchar(255),
  `gender` varchar(20),
  `date_of_birth` date,
  `status` varchar(30),
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `addresses` (
  `id` char(20) PRIMARY KEY,
  `user_id` char(20) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `province` varchar(100),
  `district` varchar(100),
  `ward` varchar(100),
  `address_line` varchar(255) NOT NULL,
  `is_default` boolean,
  `created_at` datetime
);

CREATE TABLE `categories` (
  `id` char(20) PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) UNIQUE,
  `description` varchar(255)
);

CREATE TABLE `brands` (
  `id` char(20) PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) UNIQUE,
  `country` varchar(100),
  `description` varchar(255),
  `is_active` boolean
);

CREATE TABLE `products` (
  `id` char(20) PRIMARY KEY,
  `category_id` char(20) NOT NULL,
  `brand_id` char(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) UNIQUE,
  `product_type` varchar(30) NOT NULL,
  `description` text,
  `is_active` boolean,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `product_variants` (
  `id` char(20) PRIMARY KEY,
  `product_id` char(20) NOT NULL,
  `sku` varchar(100) UNIQUE NOT NULL,
  `variant_name` varchar(150),
  `frame_style` varchar(100),
  `lens_type` varchar(100),
  `color` varchar(50),
  `size` varchar(50),
  `material` varchar(100),
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int DEFAULT 0,
  `image_3d_url` varchar(255),
  `is_active` boolean,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `product_images` (
  `id` char(20) PRIMARY KEY,
  `product_id` char(20),
  `variant_id` char(20),
  `image_url` varchar(255) NOT NULL,
  `image_type` varchar(20),
  `sort_order` int
);

CREATE TABLE `combos` (
  `id` char(20) PRIMARY KEY,
  `name` varchar(150) NOT NULL,
  `description` varchar(255),
  `combo_price` decimal(10,2) NOT NULL,
  `is_active` boolean,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `combo_items` (
  `id` char(20) PRIMARY KEY,
  `combo_id` char(20) NOT NULL,
  `product_variant_id` char(20) NOT NULL,
  `quantity` int NOT NULL
);

CREATE TABLE `vouchers` (
  `id` char(20) PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) UNIQUE NOT NULL,
  `discount_type` varchar(20),
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2),
  `max_discount_value` decimal(10,2),
  `start_at` datetime,
  `expired_at` datetime,
  `usage_limit` int,
  `is_active` boolean
);

CREATE TABLE `carts` (
  `id` char(20) PRIMARY KEY,
  `user_id` char(20) UNIQUE NOT NULL,
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `cart_items` (
  `id` char(20) PRIMARY KEY,
  `cart_id` char(20) NOT NULL,
  `product_variant_id` char(20) NOT NULL,
  `selected_color` varchar(80),
  `selected_size` varchar(80),
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
);

CREATE TABLE `orders` (
  `id` char(20) PRIMARY KEY,
  `order_code` varchar(50) UNIQUE NOT NULL,
  `user_id` char(20) NOT NULL,
  `address_id` char(20) NOT NULL,
  `order_type` varchar(30) NOT NULL,
  `voucher_id` char(20),
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0,
  `shipping_fee` decimal(10,2) DEFAULT 0,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(30),
  `payment_status` varchar(30),
  `order_status` varchar(30),
  `note` varchar(255),
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `order_items` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) NOT NULL,
  `product_variant_id` char(20),
  `combo_id` char(20),
  `item_type` varchar(20) NOT NULL,
  `item_name_snapshot` varchar(150) NOT NULL,
  `sku_snapshot` varchar(100),
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
);

CREATE TABLE `payments` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) NOT NULL,
  `payment_method` varchar(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_code` varchar(100),
  `payment_status` varchar(30),
  `paid_at` datetime,
  `created_at` datetime
);

CREATE TABLE `order_status_histories` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) NOT NULL,
  `old_status` varchar(30),
  `new_status` varchar(30) NOT NULL,
  `changed_by` char(20),
  `note` varchar(255),
  `created_at` datetime
);

CREATE TABLE `shipments` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) UNIQUE NOT NULL,
  `carrier` varchar(100),
  `tracking_code` varchar(100),
  `shipping_status` varchar(30),
  `shipped_at` datetime,
  `delivered_at` datetime,
  `note` varchar(255)
);

CREATE TABLE `pre_orders` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) UNIQUE NOT NULL,
  `expected_arrival_date` datetime,
  `supplier_note` varchar(255),
  `received_at` datetime,
  `status` varchar(30)
);

CREATE TABLE `prescriptions` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) UNIQUE NOT NULL,
  `user_id` char(20) NOT NULL,
  `sphere_left` varchar(20),
  `sphere_right` varchar(20),
  `cylinder_left` varchar(20),
  `cylinder_right` varchar(20),
  `axis_left` varchar(20),
  `axis_right` varchar(20),
  `pd` varchar(20),
  `add_power` varchar(20),
  `prescription_image` varchar(255),
  `note` varchar(255),
  `status` varchar(30),
  `verified_by` char(20),
  `verified_at` datetime
);

CREATE TABLE `prescription_workflows` (
  `id` char(20) PRIMARY KEY,
  `prescription_id` char(20) NOT NULL,
  `step_name` varchar(50) NOT NULL,
  `step_status` varchar(30),
  `handled_by` char(20),
  `note` varchar(255),
  `updated_at` datetime
);

CREATE TABLE `after_sales_requests` (
  `id` char(20) PRIMARY KEY,
  `order_id` char(20) NOT NULL,
  `user_id` char(20) NOT NULL,
  `request_type` varchar(30) NOT NULL,
  `reason` varchar(255),
  `description` text,
  `status` varchar(30),
  `created_at` datetime,
  `updated_at` datetime,
  `handled_by` char(20)
);

CREATE TABLE `refunds` (
  `id` char(20) PRIMARY KEY,
  `after_sales_request_id` char(20) NOT NULL,
  `order_id` char(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `refund_method` varchar(30),
  `refund_status` varchar(30),
  `processed_by` char(20),
  `processed_at` datetime,
  `note` varchar(255)
);

CREATE TABLE `inventory_transactions` (
  `id` char(20) PRIMARY KEY,
  `product_variant_id` char(20) NOT NULL,
  `transaction_type` varchar(30) NOT NULL,
  `quantity` int NOT NULL,
  `note` varchar(255),
  `created_by` char(20),
  `created_at` datetime
);

ALTER TABLE `users` ADD FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

ALTER TABLE `addresses` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `products` ADD FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

ALTER TABLE `products` ADD FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);

ALTER TABLE `product_variants` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `product_images` ADD FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `product_images` ADD FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `combo_items` ADD FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`);

ALTER TABLE `combo_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `carts` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`);

ALTER TABLE `cart_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`);

ALTER TABLE `orders` ADD FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `order_items` ADD FOREIGN KEY (`combo_id`) REFERENCES `combos` (`id`);

ALTER TABLE `payments` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `order_status_histories` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `order_status_histories` ADD FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

ALTER TABLE `shipments` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `pre_orders` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `prescriptions` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `prescriptions` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `prescriptions` ADD FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);

ALTER TABLE `prescription_workflows` ADD FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`);

ALTER TABLE `prescription_workflows` ADD FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`);

ALTER TABLE `after_sales_requests` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `after_sales_requests` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `after_sales_requests` ADD FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`);

ALTER TABLE `refunds` ADD FOREIGN KEY (`after_sales_request_id`) REFERENCES `after_sales_requests` (`id`);

ALTER TABLE `refunds` ADD FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `refunds` ADD FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`);

ALTER TABLE `inventory_transactions` ADD FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

ALTER TABLE `inventory_transactions` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
