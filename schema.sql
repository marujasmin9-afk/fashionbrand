-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS `clothing_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `clothing_db`;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'admin',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
  `reset_token` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `type` ENUM('clothing', 'jewelry', 'accessories') DEFAULT 'clothing',
  `image` VARCHAR(255) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Subcategories Table
CREATE TABLE IF NOT EXISTS `subcategories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Brands Table
CREATE TABLE IF NOT EXISTS `brands` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `sku` VARCHAR(100) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `subcategory_id` INT DEFAULT NULL,
  `brand_id` INT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `discount_price` DECIMAL(10,2) DEFAULT NULL,
  `stock` INT DEFAULT 0,
  `main_image` VARCHAR(255) NOT NULL,
  `short_description` TEXT DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `specifications` JSON DEFAULT NULL,
  `color` VARCHAR(100) DEFAULT NULL,
  `size` VARCHAR(100) DEFAULT NULL,
  `material` VARCHAR(100) DEFAULT NULL,
  `jewelry_type` VARCHAR(100) DEFAULT NULL,
  `rating` DECIMAL(2,1) DEFAULT 5.0,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_new` TINYINT(1) DEFAULT 0,
  `is_best_seller` TINYINT(1) DEFAULT 0,
  `is_flash_sale` TINYINT(1) DEFAULT 0,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Product Images Table
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Addresses Table
CREATE TABLE IF NOT EXISTS `addresses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `address_line1` VARCHAR(255) NOT NULL,
  `address_line2` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) NOT NULL,
  `country` VARCHAR(100) NOT NULL DEFAULT 'India',
  `pincode` VARCHAR(20) NOT NULL,
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `discount` DECIMAL(10,2) DEFAULT 0.00,
  `tax` DECIMAL(10,2) DEFAULT 0.00,
  `shipping_fee` DECIMAL(10,2) DEFAULT 0.00,
  `grand_total` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `order_status` ENUM('pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  `shipping_address` JSON NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 10. Order Items Table
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `quantity` INT NOT NULL,
  `total` DECIMAL(10,2) NOT NULL,
  `color` VARCHAR(50) DEFAULT NULL,
  `size` VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 11. Cart Table
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `session_id` VARCHAR(255) DEFAULT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `color` VARCHAR(50) DEFAULT NULL,
  `size` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 12. Wishlist Table
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_product` (`user_id`, `product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 13. Reviews Table
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `rating` INT NOT NULL,
  `comment` TEXT DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 14. Coupons Table
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `type` ENUM('percentage', 'flat') NOT NULL,
  `value` DECIMAL(10,2) NOT NULL,
  `min_order_amount` DECIMAL(10,2) DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) DEFAULT NULL,
  `expiry_date` DATE NOT NULL,
  `usage_limit` INT DEFAULT 100,
  `used_count` INT DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. Banners Table
CREATE TABLE IF NOT EXISTS `banners` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `link` VARCHAR(255) DEFAULT '#',
  `position` ENUM('home_middle', 'home_bottom', 'sale_banner', 'popup') DEFAULT 'home_middle',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 16. Sliders Table
CREATE TABLE IF NOT EXISTS `sliders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `button_text` VARCHAR(100) DEFAULT 'Shop Collection',
  `button_link` VARCHAR(255) DEFAULT 'shop.php',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 17. Blogs Table
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `image` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `author` VARCHAR(100) DEFAULT 'AURA Editorial',
  `category` VARCHAR(100) DEFAULT 'Luxury Trends',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 18. Newsletter Table
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 19. Testimonials Table
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `role` VARCHAR(100) DEFAULT 'Verified Collector',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `review` TEXT NOT NULL,
  `rating` INT DEFAULT 5,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 20. Contact Messages Table
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 21. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `site_name` VARCHAR(150) DEFAULT 'BRAND FASHION',
  `site_title` VARCHAR(255) DEFAULT 'BRAND FASHION | High Fashion & Fine Jewelry',
  `site_logo` VARCHAR(255) DEFAULT NULL,
  `favicon` VARCHAR(255) DEFAULT NULL,
  `contact_email` VARCHAR(150) DEFAULT 'concierge@auraluxe.com',
  `contact_phone` VARCHAR(50) DEFAULT '+1 (800) 888-AURA',
  `address` TEXT DEFAULT '740 Fifth Avenue, New York, NY 10019',
  `currency` VARCHAR(10) DEFAULT '$',
  `tax_rate` DECIMAL(5,2) DEFAULT 18.00,
  `free_shipping_threshold` DECIMAL(10,2) DEFAULT 500.00,
  `shipping_fee` DECIMAL(10,2) DEFAULT 25.00,
  `facebook_url` VARCHAR(255) DEFAULT 'https://facebook.com',
  `instagram_url` VARCHAR(255) DEFAULT 'https://instagram.com',
  `pinterest_url` VARCHAR(255) DEFAULT 'https://pinterest.com',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 22. Payments Table
CREATE TABLE IF NOT EXISTS `payments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `transaction_id` VARCHAR(100) NOT NULL UNIQUE,
  `payment_method` VARCHAR(50) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `raw_response` JSON DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ========================================================
-- SEED DATA INJECTION
-- ========================================================

-- Initial Admin (Password: Admin@123)
INSERT INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('Aura Concierge', 'admin@auraluxe.com', '$2y$10$wN9aL4jDbgJkS6wFqJ4LReWvS.tK.r/9iJ9B3QhYqfRk/Q61tXwly', 'superadmin');

-- Store Settings Initial Entry
INSERT INTO `settings` (`id`, `site_name`, `site_title`, `contact_email`, `contact_phone`, `currency`, `tax_rate`, `shipping_fee`) VALUES
(1, 'BRAND FASHION', 'BRAND FASHION | Haute Couture & High Jewelry', 'concierge@auraluxe.com', '+1 (800) 888-AURA', '$', 18.00, 25.00);

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `image`, `description`, `is_featured`) VALUES
(1, 'Haute Couture Dresses', 'haute-couture-dresses', 'clothing', 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&q=80', 'Exquisite evening gowns and runway dresses.', 1),
(2, 'Silk Sarees & Ethnic', 'ethnic-wear-sarees', 'clothing', 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=800&q=80', 'Royal handloom silk sarees and bridal kurtis.', 1),
(3, 'Diamond & Fine Jewelry', 'diamond-fine-jewelry', 'jewelry', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80', 'Solitaire diamond necklaces, rings, and earrings.', 1),
(4, 'Solid Gold & Pearls', 'solid-gold-pearls', 'jewelry', 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=800&q=80', '18K Gold bangles and South Sea pearl sets.', 1),
(5, 'Luxury Leather Handbags', 'luxury-handbags', 'accessories', 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&q=80', 'Handcrafted leather clutches and signature totes.', 1),
(6, 'Designer Shoes & Heels', 'designer-shoes', 'accessories', 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&q=80', 'Stiletto heels, crystal pumps, and calfskin boots.', 1);

-- Subcategories
INSERT INTO `subcategories` (`id`, `category_id`, `name`, `slug`) VALUES
(1, 1, 'Evening Gowns', 'evening-gowns'),
(2, 1, 'Cocktail Dresses', 'cocktail-dresses'),
(3, 2, 'Banarasi Silk Sarees', 'banarasi-silk-sarees'),
(4, 2, 'Embroidered Kurtis', 'embroidered-kurtis'),
(5, 3, 'Diamond Rings', 'diamond-rings'),
(6, 3, 'High Jewelry Necklaces', 'high-jewelry-necklaces'),
(7, 4, 'Gold Bangles', 'gold-bangles'),
(8, 4, 'Pearl Earrings', 'pearl-earrings'),
(9, 5, 'Structured Leather Totes', 'leather-totes'),
(10, 6, 'Crystal Embellished Heels', 'crystal-heels');

-- Brands
INSERT INTO `brands` (`id`, `name`, `logo`) VALUES
(1, 'Maison AURA', 'aura-logo.png'),
(2, 'Chanel', 'chanel-logo.png'),
(3, 'Cartier', 'cartier-logo.png'),
(4, 'Bulgari', 'bulgari-logo.png'),
(5, 'Sabyasachi', 'sabyasachi-logo.png');

-- Sliders
INSERT INTO `sliders` (`title`, `subtitle`, `image`, `button_text`, `button_link`, `sort_order`) VALUES
('The Royal Gold & Diamond Affair', 'Discover 2026 High Jewelry Collection', 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=1600&q=80', 'Explore Fine Jewelry', 'shop.php?category=diamond-fine-jewelry', 1),
('Haute Couture Autumn / Winter', 'Unrivaled Elegance & Silk Masterpieces', 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=1600&q=80', 'View Runway Dresses', 'shop.php?category=haute-couture-dresses', 2);

-- Products Seed Data
INSERT INTO `products` (`id`, `title`, `slug`, `sku`, `category_id`, `subcategory_id`, `brand_id`, `price`, `discount_price`, `stock`, `main_image`, `short_description`, `description`, `color`, `size`, `material`, `jewelry_type`, `rating`, `is_featured`, `is_new`, `is_best_seller`, `is_flash_sale`) VALUES
(1, 'Velvet Midnight Black Evening Gown', 'velvet-midnight-black-evening-gown', 'SKU-DRS-001', 1, 1, 1, 1250.00, 980.00, 15, 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800&q=80', 'A floor-sweeping French velvet gown adorned with subtle crystal trim.', 'Crafted in Paris with luscious midnight velvet, featuring a plunging corseted back and tailored fit.', 'Black', 'M, L, S', 'Velvet & Silk', NULL, 4.9, 1, 1, 1, 0),
(2, 'Royal Solitaire 2.5ct Diamond Ring', 'royal-solitaire-diamond-ring', 'SKU-JWL-001', 3, 5, 3, 4800.00, 4250.00, 5, 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=800&q=80', '18K White Gold solitaire diamond ring with VVS1 brilliance.', 'Featuring an exceptional 2.5-carat round brilliant solitaire set on a pavé diamond band.', 'Silver/White', 'Free Size', 'Diamond & 18K White Gold', 'Rings', 5.0, 1, 1, 1, 1),
(3, 'Handloom Banarasi Crimson Silk Saree', 'handloom-banarasi-crimson-silk-saree', 'SKU-SAR-001', 2, 3, 5, 890.00, 750.00, 20, 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=800&q=80', 'Pure Katan Silk Saree infused with intricate real gold zari weave.', 'Woven by master artisans in Varanasi, featuring traditional floral jaal and grand pallu.', 'Crimson Red', 'Free Size', 'Pure Katan Silk', NULL, 4.8, 1, 0, 1, 0),
(4, 'Imperial Emerald & Diamond Cascade Necklace', 'imperial-emerald-diamond-cascade-necklace', 'SKU-JWL-002', 3, 6, 4, 8500.00, 7800.00, 3, 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80', 'Columbian natural emeralds surrounded by baguette diamond cascades.', 'A masterpiece of fine gemology, suspended from platinum chains with adjustable clasp.', 'Gold & Green', 'Standard', 'Diamond & Emerald', 'Necklaces', 5.0, 1, 1, 1, 1),
(5, 'South Sea Pearl & 18K Gold Drop Earrings', 'south-sea-pearl-gold-drop-earrings', 'SKU-JWL-003', 4, 8, 3, 1450.00, 1200.00, 12, 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=800&q=80', 'Lustrous 14mm Golden South Sea pearls mounted on diamond-encrusted 18K gold.', 'Unblemished pearl drops offering an opulent glow for formal evening attire.', 'Gold', 'Standard', 'Pearl & 18K Gold', 'Earrings', 4.9, 1, 0, 0, 0),
(6, 'Quilted Lambskin Gold-Chain Shoulder Bag', 'quilted-lambskin-gold-chain-bag', 'SKU-BAG-001', 5, 9, 2, 2950.00, NULL, 8, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&q=80', 'Italian butter-soft lambskin leather tote with 24K gold plated chain.', 'Iconic quilted construction, dual interior compartments, and signature clasp.', 'Black', 'One Size', 'Calfskin Leather', NULL, 5.0, 1, 1, 1, 0),
(7, 'Satin Crystal Stiletto Pumps 105mm', 'satin-crystal-stiletto-pumps', 'SKU-SHS-001', 6, 10, 1, 980.00, 850.00, 10, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&q=80', 'Silk satin high heel pumps accented with Swarovski crystal buckle.', 'Handmade in Italy with leather sole and sleek 105mm stiletto silhouette.', 'Champagne Gold', '37, 38, 39', 'Silk & Crystal', NULL, 4.7, 1, 0, 0, 1),
(8, 'Structured Gold Filigree Bangle Bracelet', 'structured-gold-filigree-bangle', 'SKU-JWL-004', 4, 7, 4, 2100.00, 1850.00, 7, 'https://images.unsplash.com/photo-1611591475116-2a7f516422d2?w=800&q=80', 'Solid 22K yellow gold bangle displaying royal Indian filigree art.', 'Intricate hand-carved details with secure safety latch mechanism.', 'Yellow Gold', 'Standard', 'Gold', 'Bangles', 4.9, 0, 1, 1, 0);

-- Product Images Seed
INSERT INTO `product_images` (`product_id`, `image_url`) VALUES
(1, 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800&q=80'),
(1, 'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&q=80'),
(2, 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=800&q=80'),
(4, 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&q=80');

-- Coupons Seed
INSERT INTO `coupons` (`code`, `type`, `value`, `min_order_amount`, `max_discount`, `expiry_date`, `usage_limit`, `status`) VALUES
('AURA10', 'percentage', 10.00, 500.00, 200.00, '2027-12-31', 500, 'active'),
('LUXURY50', 'flat', 50.00, 1000.00, 50.00, '2027-12-31', 200, 'active');

-- Banners Seed
INSERT INTO `banners` (`title`, `subtitle`, `image`, `link`, `position`) VALUES
('High Fashion Autumn Capsule', 'Limited Couture Pieces', 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&q=80', 'shop.php', 'home_middle'),
('Fine Diamond Showcase', 'Up to 20% Off Select Jewels', 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=1200&q=80', 'jewelry.php', 'sale_banner');

-- Testimonials Seed
INSERT INTO `testimonials` (`name`, `role`, `avatar`, `review`, `rating`) VALUES
('Lady Eleanor Vance', 'Haute Couture Collector', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&q=80', 'BRAND FASHION provides unparalleled quality. The emerald cascade necklace arrived in immaculate custom packaging.', 5),
('Sophia Sterling', 'Fashion Director', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=200&q=80', 'The velvet evening gown fit perfectly right out of the box. Truly a Dior level luxury experience.', 5);

-- Blogs Seed
INSERT INTO `blogs` (`title`, `slug`, `image`, `content`, `category`) VALUES
('Styling Solitaire Diamonds for Black-Tie Galas', 'styling-solitaire-diamonds-black-tie-galas', 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80', '<p>When attending high-society galas, your jewelry choice establishes your personal signature...</p>', 'Fine Jewelry Guide'),
('The Art of Handwoven Silk Sarees', 'art-of-handwoven-silk-sarees', 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?w=800&q=80', '<p>Every Banarasi saree carries centuries of heritage crafted by dedicated master weavers...</p>', 'Heritage Couture');
