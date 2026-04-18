-- =====================================================
-- AURA DATING APPLICATION - DATABASE SCHEMA
-- =====================================================
-- Database: aura_dating
-- Created: 2026-03-05
-- Version: 1.0
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Table: users
-- Core authentication and basic user information
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE COMMENT 'Unique identifier for external use',
    `email` VARCHAR(255) UNIQUE,
    `phone` VARCHAR(20) UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Account active status',
    `is_verified` TINYINT(1) DEFAULT 0 COMMENT 'Email/Phone verified',
    `is_premium` TINYINT(1) DEFAULT 0 COMMENT 'Premium subscription status',
    `premium_expires_at` DATETIME NULL COMMENT 'Premium subscription expiry',
    `role` ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    `last_active_at` DATETIME NULL COMMENT 'Last activity timestamp',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP NULL,
    INDEX `idx_email` (`email`),
    INDEX `idx_phone` (`phone`),
    INDEX `idx_uuid` (`uuid`),
    INDEX `idx_is_premium` (`is_premium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: user_profiles
-- Extended profile information for dating
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100),
    `date_of_birth` DATE NOT NULL COMMENT 'Must be 18+ to use app',
    `gender` ENUM('male', 'female', 'non_binary', 'other') NOT NULL,
    `interested_in` ENUM('male', 'female', 'everyone') DEFAULT 'everyone',
    `bio` TEXT COMMENT 'User bio/description',
    `latitude` DECIMAL(10, 8) COMMENT 'Current latitude',
    `longitude` DECIMAL(11, 8) COMMENT 'Current longitude',
    `location_updated_at` DATETIME NULL,
    `max_distance` INT DEFAULT 50 COMMENT 'Discovery distance in km',
    `min_age` INT DEFAULT 18,
    `max_age` INT DEFAULT 100,
    `profile_completed` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_location` (`latitude`, `longitude`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: profile_images
-- Multiple images per user profile
-- -----------------------------------------------------
DROP TABLE IF EXISTS `profile_images`;
CREATE TABLE `profile_images` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `image_url` VARCHAR(500) NOT NULL,
    `thumbnail_url` VARCHAR(500),
    `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Primary display image',
    `order` INT DEFAULT 0,
    `is_verified` TINYINT(1) DEFAULT 0 COMMENT 'Verified by admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: hobbies
-- Predefined list of user hobbies/interests
-- -----------------------------------------------------
DROP TABLE IF EXISTS `hobbies`;
CREATE TABLE `hobbies` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL UNIQUE,
    `icon` VARCHAR(100) COMMENT 'Icon identifier',
    `category` VARCHAR(50),
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: user_hobbies
-- User-selected hobbies (many-to-many)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_hobbies`;
CREATE TABLE `user_hobbies` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `hobby_id` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`hobby_id`) REFERENCES `hobbies`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_hobby` (`user_id`, `hobby_id`),
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_hobby_id` (`hobby_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: user_swipes
-- Track user swipe actions (like/pass)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_swipes`;
CREATE TABLE `user_swipes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `swiper_id` BIGINT UNSIGNED NOT NULL COMMENT 'User performing the swipe',
    `swiped_id` BIGINT UNSIGNED NOT NULL COMMENT 'User being swiped on',
    `action` ENUM('like', 'pass', 'super_like') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`swiper_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`swiped_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_swipe` (`swiper_id`, `swiped_id`),
    INDEX `idx_swiper_id` (`swiper_id`),
    INDEX `idx_swiped_id` (`swiped_id`),
    INDEX `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: matches
-- Mutual likes (matches) between users
-- -----------------------------------------------------
DROP TABLE IF EXISTS `matches`;
CREATE TABLE `matches` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid` CHAR(36) NOT NULL UNIQUE,
    `user_one_id` BIGINT UNSIGNED NOT NULL,
    `user_two_id` BIGINT UNSIGNED NOT NULL,
    `user_one_super_like` TINYINT(1) DEFAULT 0,
    `user_two_super_like` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1 COMMENT 'Match still active',
    `unmatched_by` BIGINT UNSIGNED NULL COMMENT 'User who unmatched',
    `unmatched_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_one_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_two_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_user_pair` (`user_one_id`, `user_two_id`),
    INDEX `idx_user_one_id` (`user_one_id`),
    INDEX `idx_user_two_id` (`user_two_id`),
    INDEX `idx_uuid` (`uuid`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: conversations
-- Chat threads between matched users
-- -----------------------------------------------------
DROP TABLE IF EXISTS `conversations`;
CREATE TABLE `conversations` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `match_id` BIGINT UNSIGNED NOT NULL,
    `last_message_at` DATETIME NULL,
    `last_message_preview` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`match_id`) REFERENCES `matches`(`id`) ON DELETE CASCADE,
    INDEX `idx_match_id` (`match_id`),
    INDEX `idx_last_message_at` (`last_message_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: messages
-- Individual messages in conversations
-- -----------------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` BIGINT UNSIGNED NOT NULL,
    `sender_id` BIGINT UNSIGNED NOT NULL,
    `receiver_id` BIGINT UNSIGNED NOT NULL,
    `message_type` ENUM('text', 'image', 'gif', 'audio') DEFAULT 'text',
    `message_content` TEXT NOT NULL,
    `media_url` VARCHAR(500),
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` DATETIME NULL,
    `is_deleted` TINYINT(1) DEFAULT 0,
    `deleted_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `conversations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_conversation_id` (`conversation_id`),
    INDEX `idx_sender_id` (`sender_id`),
    INDEX `idx_receiver_id` (`receiver_id`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: user_reports
-- User-reported profiles
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_reports`;
CREATE TABLE `user_reports` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `reporter_id` BIGINT UNSIGNED NOT NULL,
    `reported_id` BIGINT UNSIGNED NOT NULL,
    `reason` ENUM(
        'fake_profile',
        'inappropriate_content',
        'harassment',
        'spam',
        'underage',
        'other'
    ) NOT NULL,
    `description` TEXT,
    `status` ENUM('pending', 'reviewed', 'action_taken', 'dismissed') DEFAULT 'pending',
    `reviewed_by` BIGINT UNSIGNED NULL,
    `reviewed_at` DATETIME NULL,
    `admin_notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reported_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_reporter_id` (`reporter_id`),
    INDEX `idx_reported_id` (`reported_id`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: subscriptions
-- Premium subscription plans
-- -----------------------------------------------------
DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `plan` ENUM('free', 'gold', 'platinum') DEFAULT 'free',
    `stripe_subscription_id` VARCHAR(255),
    `stripe_customer_id` VARCHAR(255),
    `started_at` DATETIME NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `auto_renew` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_active` (`is_active`),
    INDEX `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: otp_verifications
-- Phone/Email OTP verification codes
-- -----------------------------------------------------
DROP TABLE IF EXISTS `otp_verifications`;
CREATE TABLE `otp_verifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `identifier` VARCHAR(255) NOT NULL COMMENT 'Email or phone number',
    `identifier_type` ENUM('email', 'phone') NOT NULL,
    `otp_code` VARCHAR(10) NOT NULL,
    `is_verified` TINYINT(1) DEFAULT 0,
    `expires_at` DATETIME NOT NULL,
    `verified_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_identifier` (`identifier`),
    INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: notifications
-- In-app notifications
-- -----------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('match', 'message', 'like', 'super_like', 'profile_view', 'system') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `body` TEXT,
    `data` JSON,
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table: user_blocks
-- Blocked users
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user_blocks`;
CREATE TABLE `user_blocks` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `blocker_id` BIGINT UNSIGNED NOT NULL COMMENT 'User doing the blocking',
    `blocked_id` BIGINT UNSIGNED NOT NULL COMMENT 'User being blocked',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`blocker_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`blocked_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_block` (`blocker_id`, `blocked_id`),
    INDEX `idx_blocker_id` (`blocker_id`),
    INDEX `idx_blocked_id` (`blocked_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Insert default hobbies
-- -----------------------------------------------------
INSERT INTO `hobbies` (`name`, `category`) VALUES
('Hiking', 'outdoor'),
('Photography', 'creative'),
('Cooking', 'lifestyle'),
('Traveling', 'outdoor'),
('Reading', 'intellectual'),
('Gaming', 'entertainment'),
('Fitness', 'health'),
('Yoga', 'health'),
('Music', 'entertainment'),
('Movies', 'entertainment'),
('Dancing', 'creative'),
('Art', 'creative'),
('Writing', 'creative'),
('Coding', 'intellectual'),
('Gardening', 'lifestyle'),
('Pets', 'lifestyle'),
('Coffee', 'lifestyle'),
('Wine', 'lifestyle'),
('Sports', 'health'),
('Running', 'health'),
('Swimming', 'health'),
('Cycling', 'outdoor'),
('Camping', 'outdoor'),
('Skiing', 'outdoor'),
('Meditation', 'health'),
('Fashion', 'lifestyle'),
('Shopping', 'lifestyle'),
('Volunteering', 'social'),
('Board Games', 'entertainment');

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- END OF DATABASE SCHEMA
-- =====================================================
