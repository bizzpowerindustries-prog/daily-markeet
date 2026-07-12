-- DailyMart Database Schema
-- MySQL 8.0

CREATE DATABASE IF NOT EXISTS dailymart;
USE dailymart;

-- Users Table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL UNIQUE,
    phone VARCHAR(20) NULL UNIQUE,
    avatar VARCHAR(255) NULL,
    email_verified_at TIMESTAMP NULL,
    phone_verified_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    fcm_token VARCHAR(255) NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_firebase_uid (firebase_uid),
    INDEX idx_email (email),
    INDEX idx_phone (phone)
);

-- Sellers Table
CREATE TABLE sellers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    shop_name VARCHAR(255) NOT NULL,
    shop_slug VARCHAR(255) UNIQUE NOT NULL,
    shop_description TEXT NULL,
    shop_logo VARCHAR(255) NULL,
    shop_banner VARCHAR(255) NULL,
    cnic VARCHAR(20) NULL,
    ntn VARCHAR(20) NULL,
    business_registration VARCHAR(255) NULL,
    bank_name VARCHAR(255) NULL,
    bank_account_title VARCHAR(255) NULL,
    iban VARCHAR(34) NULL,
    shop_address TEXT NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    zip_code VARCHAR(20) NULL,
    commission_override DECIMAL(5,2) NULL,
    status ENUM('pending', 'approved', 'suspended', 'banned') DEFAULT 'pending',
    approved_at TIMESTAMP NULL,
    suspended_at TIMESTAMP NULL,
    documents JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_shop_slug (shop_slug),
    INDEX idx_status (status)
);

-- Categories Table
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    banner VARCHAR(255) NULL,
    icon VARCHAR(255) NULL,
    commission_percent DECIMAL(5,2) DEFAULT 10.00,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    meta_keywords VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_parent_id (parent_id),
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
);

-- Brands Table
CREATE TABLE brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    logo VARCHAR(255) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
);

-- Products Table
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    brand_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL,
    sale_price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    specs JSON NULL,
    features JSON NULL,
    weight DECIMAL(10,2) NULL,
    length DECIMAL(10,2) NULL,
    width DECIMAL(10,2) NULL,
    height DECIMAL(10,2) NULL,
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    meta_keywords VARCHAR(255) NULL,
    views INT DEFAULT 0,
    sales_count INT DEFAULT 0,
    rating DECIMAL(2,1) DEFAULT 0,
    review_count INT DEFAULT 0,
    status ENUM('pending', 'active', 'inactive', 'deleted') DEFAULT 'pending',
    is_approved BOOLEAN DEFAULT FALSE,
    approved_at TIMESTAMP NULL,
    is_flash_sale BOOLEAN DEFAULT FALSE,
    flash_sale_ends_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    INDEX idx_seller_id (seller_id),
    INDEX idx_category_id (category_id),
    INDEX idx_brand_id (brand_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_is_approved (is_approved),
    INDEX idx_sale_price (sale_price),
    INDEX idx_created_at (created_at),
    FULLTEXT idx_search (name, description)
);

-- Product Images Table
CREATE TABLE product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    path VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id)
);

-- Product Variants Table
CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    value VARCHAR(255) NOT NULL,
    price DECIMAL(12,2) NULL,
    stock INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id)
);

-- Addresses Table
CREATE TABLE addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NOT NULL,
    zip_code VARCHAR(20) NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);

-- Orders Table
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    tracking_number VARCHAR(50) NULL UNIQUE,
    subtotal DECIMAL(12,2) NOT NULL,
    shipping_fee DECIMAL(12,2) DEFAULT 0,
    tax DECIMAL(12,2) DEFAULT 0,
    discount DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_gateway VARCHAR(50) NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(255) NULL,
    shipping_address_id BIGINT UNSIGNED NOT NULL,
    billing_address_id BIGINT UNSIGNED NULL,
    courier VARCHAR(100) DEFAULT 'Pakistan Post',
    expected_delivery_date DATE NULL,
    status ENUM('pending', 'processing', 'packed', 'shipped', 'delivered', 'cancelled', 'returned') DEFAULT 'pending',
    coupon_code VARCHAR(50) NULL,
    notes TEXT NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_address_id) REFERENCES addresses(id),
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id),
    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_tracking_number (tracking_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
);

-- Order Items Table
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (seller_id) REFERENCES sellers(id),
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    INDEX idx_seller_id (seller_id)
);

-- Order Tracking Events Table
CREATE TABLE order_tracking_events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    location VARCHAR(255) NULL,
    description TEXT NULL,
    event_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id)
);

-- Wallets Table
CREATE TABLE wallets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id BIGINT UNSIGNED NOT NULL,
    owner_type VARCHAR(50) NOT NULL,
    balance DECIMAL(12,2) DEFAULT 0,
    is_frozen BOOLEAN DEFAULT FALSE,
    frozen_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_owner (owner_id, owner_type)
);

-- Wallet Transactions Table
CREATE TABLE wallet_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wallet_id BIGINT UNSIGNED NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    balance_after DECIMAL(12,2) NOT NULL,
    source VARCHAR(50) NOT NULL,
    source_id BIGINT UNSIGNED NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    INDEX idx_wallet_id (wallet_id),
    INDEX idx_source (source, source_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Withdraw Requests Table
CREATE TABLE withdraw_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    wallet_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    fee DECIMAL(12,2) DEFAULT 0,
    net_amount DECIMAL(12,2) NOT NULL,
    bank_name VARCHAR(255) NOT NULL,
    account_title VARCHAR(255) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    iban VARCHAR(34) NULL,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    approved_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    INDEX idx_wallet_id (wallet_id),
    INDEX idx_status (status)
);

-- Payment Gateways Table
CREATE TABLE payment_gateways (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    credentials JSON NOT NULL,
    fee_percent DECIMAL(5,2) DEFAULT 0,
    fee_fixed DECIMAL(10,2) DEFAULT 0,
    is_test_mode BOOLEAN DEFAULT TRUE,
    status BOOLEAN DEFAULT TRUE,
    priority INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_priority (priority)
);

-- Insert default payment gateways
INSERT INTO payment_gateways (name, display_name, credentials, fee_percent, fee_fixed, is_test_mode, status, priority) VALUES
('payfast', 'Payfast', '{"merchant_id":"demo","merchant_key":"demo","passphrase":"demo","test_mode":true}', 2.50, 5.00, true, true, 1),
('rapidgateway', 'RapidGateway', '{"api_key":"demo","api_secret":"demo","test_mode":true}', 2.00, 0, true, false, 2),
('simpaisa', 'Simpaisa', '{"merchant_id":"demo","api_key":"demo","test_mode":true}', 2.00, 0, true, false, 3);

-- Coupons Table
CREATE TABLE coupons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    discount_type ENUM('percentage', 'fixed', 'free_shipping') NOT NULL,
    discount_value DECIMAL(12,2) NOT NULL,
    max_discount DECIMAL(12,2) NULL,
    min_order DECIMAL(12,2) DEFAULT 0,
    starts_at TIMESTAMP NOT NULL,
    ends_at TIMESTAMP NOT NULL,
    usage_limit INT DEFAULT 1,
    usage_count INT DEFAULT 0,
    per_user_limit INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    applicable_categories JSON NULL,
    applicable_products JSON NULL,
    excluded_products JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_is_active (is_active),
    INDEX idx_dates (starts_at, ends_at)
);

-- Reviews Table
CREATE TABLE reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255) NULL,
    comment TEXT NULL,
    images JSON NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    approved_at TIMESTAMP NULL,
    reply TEXT NULL,
    replied_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_rating (rating),
    INDEX idx_is_approved (is_approved)
);

-- Cart Table
CREATE TABLE cart (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart (user_id, product_id),
    INDEX idx_user_id (user_id)
);

-- Wishlist Table
CREATE TABLE wishlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, product_id)
);

-- Notifications Table
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    data JSON NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
);

-- Settings Table
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NOT NULL,
    group VARCHAR(100) DEFAULT 'general',
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (key),
    INDEX idx_group (group)
);

-- Insert default settings
INSERT INTO settings (key, value, group, is_public) VALUES
('site_name', 'DailyMart.pk', 'general', true),
('site_logo', '/images/logo.png', 'general', true),
('favicon', '/images/favicon.ico', 'general', true),
('primary_color', '#FF5722', 'branding', true),
('secondary_color', '#212121', 'branding', true),
('commission_global', '10.00', 'commission', false),
('commission_category_default', '10.00', 'commission', false),
('min_withdraw_amount', '1000.00', 'wallet', true),
('withdraw_fee', '50.00', 'wallet', true),
('min_add_money', '100.00', 'wallet', true),
('return_days', '7', 'policy', true),
('currency', 'PKR', 'general', true);

-- Banner Sliders Table
CREATE TABLE banners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NULL,
    image VARCHAR(255) NOT NULL,
    link VARCHAR(255) NULL,
    type VARCHAR(50) DEFAULT 'hero',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    starts_at TIMESTAMP NULL,
    ends_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
);

-- Flash Sales Table
CREATE TABLE flash_sales (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    banner VARCHAR(255) NULL,
    discount_percent DECIMAL(5,2) NOT NULL,
    starts_at TIMESTAMP NOT NULL,
    ends_at TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active),
    INDEX idx_dates (starts_at, ends_at)
);

-- Flash Sale Products Table
CREATE TABLE flash_sale_products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    flash_sale_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    flash_price DECIMAL(12,2) NOT NULL,
    flash_stock INT NOT NULL,
    sold_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (flash_sale_id) REFERENCES flash_sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_flash_product (flash_sale_id, product_id),
    INDEX idx_flash_sale_id (flash_sale_id)
);

-- Return Requests Table
CREATE TABLE return_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    return_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    images JSON NULL,
    approved_at TIMESTAMP NULL,
    rejected_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);

-- Return Items Table
CREATE TABLE return_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    return_id BIGINT UNSIGNED NOT NULL,
    order_item_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (return_id) REFERENCES return_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES order_items(id),
    INDEX idx_return_id (return_id)
);

-- Chats Table
CREATE TABLE chats (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id BIGINT UNSIGNED NOT NULL,
    receiver_id BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'text',
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sender_id (sender_id),
    INDEX idx_receiver_id (receiver_id),
    INDEX idx_created_at (created_at)
);

-- Email Logs Table
CREATE TABLE email_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NULL,
    status ENUM('pending', 'sent', 'failed', 'queued') DEFAULT 'pending',
    error TEXT NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_recipient (recipient),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Error Logs Table
CREATE TABLE error_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    file VARCHAR(255) NULL,
    line INT NULL,
    stack_trace TEXT NULL,
    context JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
);

-- Dummy Data
-- Insert categories
INSERT INTO categories (name, slug, icon, commission_percent, is_active) VALUES
('Electronics', 'electronics', 'fa-mobile-alt', 10.00, true),
('Fashion', 'fashion', 'fa-tshirt', 12.00, true),
('Home & Kitchen', 'home-kitchen', 'fa-home', 10.00, true),
('Beauty & Health', 'beauty-health', 'fa-spa', 12.00, true),
('Sports & Outdoors', 'sports-outdoors', 'fa-running', 10.00, true),
('Books & Stationery', 'books-stationery', 'fa-book', 8.00, true);

-- Insert subcategories
INSERT INTO categories (parent_id, name, slug, icon, commission_percent, is_active) VALUES
(1, 'Mobile Phones', 'mobile-phones', 'fa-mobile', 10.00, true),
(1, 'Laptops', 'laptops', 'fa-laptop', 10.00, true),
(1, 'Headphones', 'headphones', 'fa-headphones', 10.00, true),
(2, 'Men\'s Clothing', 'mens-clothing', 'fa-male', 12.00, true),
(2, 'Women\'s Clothing', 'womens-clothing', 'fa-female', 12.00, true),
(2, 'Footwear', 'footwear', 'fa-shoe-prints', 12.00, true);

-- Insert brands
INSERT INTO brands (name, slug, is_active) VALUES
('Samsung', 'samsung', true),
('Apple', 'apple', true),
('Nike', 'nike', true),
('Adidas', 'adidas', true),
('Sony', 'sony', true),
('Dell', 'dell', true),
('HP', 'hp', true),
('Lenovo', 'lenovo', true);

-- Insert dummy users (with firebase_uid as placeholder)
INSERT INTO users (firebase_uid, name, email, phone, is_active) VALUES
('firebase_user_1', 'John Doe', 'john@example.com', '03001234567', true),
('firebase_user_2', 'Jane Smith', 'jane@example.com', '03011234567', true),
('firebase_user_3', 'Ahmed Khan', 'ahmed@example.com', '03021234567', true),
('firebase_user_4', 'Maria Ahmed', 'maria@example.com', '03031234567', true),
('firebase_seller_1', 'Tech Store', 'tech@example.com', '03041234567', true),
('firebase_seller_2', 'Fashion Hub', 'fashion@example.com', '03051234567', true);

-- Insert dummy sellers
INSERT INTO sellers (user_id, shop_name, shop_slug, shop_description, bank_name, iban, status, approved_at) VALUES
(5, 'Tech Store Pakistan', 'tech-store', 'Your one-stop shop for electronics', 'HBL', 'PK36HBLV0123456789012345', 'approved', NOW()),
(6, 'Fashion Hub', 'fashion-hub', 'Latest fashion trends', 'UBL', 'PK36UBLV0123456789012345', 'approved', NOW());

-- Insert dummy products
INSERT INTO products (seller_id, category_id, brand_id, name, slug, description, price, sale_price, stock, status, is_approved, approved_at, rating, review_count) VALUES
(1, 2, 1, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'Latest Samsung flagship with AI features', 159999.00, 149999.00, 50, 'active', true, NOW(), 4.8, 150),
(1, 2, 2, 'Apple iPhone 15 Pro Max', 'apple-iphone-15-pro-max', 'Apple\'s most powerful iPhone yet', 189999.00, 179999.00, 30, 'active', true, NOW(), 4.9, 200),
(2, 5, 3, 'Nike Air Max 270', 'nike-air-max-270', 'Comfortable running shoes', 25000.00, 21999.00, 100, 'active', true, NOW(), 4.5, 80),
(1, 3, 4, 'Sony WH-1000XM5', 'sony-wh-1000xm5', 'Premium noise-cancelling headphones', 45000.00, 39999.00, 25, 'active', true, NOW(), 4.7, 120),
(2, 6, 3, 'Adidas Ultraboost 22', 'adidas-ultraboost-22', 'Ultra comfortable running shoes', 28000.00, 23999.00, 75, 'active', true, NOW(), 4.6, 65);

-- Insert dummy product images
INSERT INTO product_images (product_id, path, is_primary) VALUES
(1, '/products/samsung-s24-1.jpg', true),
(1, '/products/samsung-s24-2.jpg', false),
(1, '/products/samsung-s24-3.jpg', false),
(2, '/products/iphone-15-1.jpg', true),
(2, '/products/iphone-15-2.jpg', false);

-- Insert dummy addresses
INSERT INTO addresses (user_id, name, phone, address, city, state, country, zip_code, is_default) VALUES
(1, 'John Doe', '03001234567', 'House #123, Street 45, Phase 6', 'Islamabad', 'Federal', 'Pakistan', '44000', true),
(2, 'Jane Smith', '03011234567', 'House #456, Block B, Gulberg', 'Lahore', 'Punjab', 'Pakistan', '54000', true),
(3, 'Ahmed Khan', '03021234567', 'Shop #789, Saddar Road', 'Karachi', 'Sindh', 'Pakistan', '74400', true);

-- Insert wallet records
INSERT INTO wallets (owner_id, owner_type, balance) VALUES
(1, 'App\\Models\\User', 5000.00),
(2, 'App\\Models\\User', 3000.00),
(3, 'App\\Models\\User', 1000.00),
(1, 'App\\Models\\Seller', 150000.00),
(2, 'App\\Models\\Seller', 80000.00);

-- Insert dummy orders
INSERT INTO orders (user_id, order_number, tracking_number, subtotal, shipping_fee, tax, discount, total, payment_method, payment_status, shipping_address_id, status, created_at) VALUES
(1, 'DM-ORD-001', 'DM-TRK-001', 149999.00, 200.00, 7500.00, 0, 157699.00, 'cash_on_delivery', 'pending', 1, 'pending', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'DM-ORD-002', 'DM-TRK-002', 21999.00, 200.00, 1100.00, 500.00, 22799.00, 'gateway', 'paid', 2, 'processing', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'DM-ORD-003', 'DM-TRK-003', 39999.00, 200.00, 2000.00, 1000.00, 41199.00, 'wallet', 'paid', 1, 'shipped', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Insert order items
INSERT INTO order_items (order_id, product_id, seller_id, quantity, price, total) VALUES
(1, 1, 1, 1, 149999.00, 149999.00),
(2, 3, 2, 1, 21999.00, 21999.00),
(3, 4, 1, 1, 39999.00, 39999.00);

-- Insert order tracking events
INSERT INTO order_tracking_events (order_id, status, description, location, event_time) VALUES
(1, 'pending', 'Order placed successfully', 'System', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'pending', 'Order placed successfully', 'System', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'processing', 'Order is being processed', 'Warehouse', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'pending', 'Order placed successfully', 'System', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 'shipped', 'Order has been shipped', 'Karachi', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Insert dummy reviews
INSERT INTO reviews (product_id, user_id, rating, comment, is_approved, approved_at, created_at) VALUES
(1, 1, 5, 'Excellent phone! Best I have ever used.', true, NOW(), DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 2, 4, 'Great phone but a bit expensive.', true, NOW(), DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 3, 5, 'Very comfortable shoes.', true, NOW(), DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Insert dummy coupons
INSERT INTO coupons (code, name, discount_type, discount_value, max_discount, min_order, starts_at, ends_at, usage_limit, is_active) VALUES
('SAVE10', '10% Off Everything', 'percentage', 10.00, 1000.00, 100.00, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 100, true),
('FREESHIP', 'Free Shipping', 'free_shipping', 0, 0, 500.00, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 15 DAY), 50, true),
('WELCOME500', 'Welcome Discount', 'fixed', 500.00, 500.00, 1000.00, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 60 DAY), 50, true);

-- Insert dummy banners
INSERT INTO banners (title, subtitle, image, link, type, sort_order, is_active) VALUES
('Summer Sale', 'Up to 50% off on selected items', '/banners/summer-sale.jpg', '/sale', 'hero', 1, true),
('New Arrivals', 'Check out our latest products', '/banners/new-arrivals.jpg', '/new-arrivals', 'hero', 2, true),
('Eid Special', 'Special discounts for Eid', '/banners/eid-special.jpg', '/eid-sale', 'hero', 3, true);

-- Insert dummy flash sale
INSERT INTO flash_sales (title, description, discount_percent, starts_at, ends_at, is_active) VALUES
('Flash Sale - Today Only', 'Huge discounts for 24 hours', 20.00, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 1 DAY), true);

-- Insert flash sale products
INSERT INTO flash_sale_products (flash_sale_id, product_id, flash_price, flash_stock) VALUES
(1, 1, 119999.00, 10),
(1, 3, 17599.00, 20),
(1, 4, 31999.00, 15);

-- Insert dummy settings for commissions
INSERT INTO settings (key, value, group, is_public) VALUES
('commission_seller_1', '8.00', 'commission', false),
('commission_category_1', '9.00', 'commission', false),
('commission_global_override', '10.00', 'commission', false);
