-- Maharani Mobil Database (MySQL)
-- Generated for Laragon

CREATE DATABASE IF NOT EXISTS `maharani_mobil`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `maharani_mobil`;

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `test_drives`;
DROP TABLE IF EXISTS `offers`;
DROP TABLE IF EXISTS `cars`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `phone` VARCHAR(255) NULL,
  `role` ENUM('customer','marketing','supervisor','owner') NOT NULL DEFAULT 'customer',
  `provider` VARCHAR(255) NULL,
  `provider_id` VARCHAR(255) NULL,
  `avatar` VARCHAR(255) NULL,
  `email_verified_at` TIMESTAMP NULL,
  `password` VARCHAR(255) NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `sessions_user_id_index` (`user_id`),
  INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  INDEX `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  INDEX `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT NULL,
  `cancelled_at` INT NULL,
  `created_at` INT NOT NULL,
  `finished_at` INT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL UNIQUE,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cars` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_unit` VARCHAR(255) NOT NULL UNIQUE,
  `merk` VARCHAR(255) NOT NULL,
  `tipe` VARCHAR(255) NOT NULL,
  `tahun` INT UNSIGNED NOT NULL,
  `harga` DECIMAL(15,2) NOT NULL,
  `kilometer` INT UNSIGNED NULL,
  `transmisi` VARCHAR(255) NULL,
  `warna` VARCHAR(255) NULL,
  `bahan_bakar` VARCHAR(255) NULL,
  `status` ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
  `deskripsi` TEXT NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `cars_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `favorites` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `car_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `favorites_user_id_car_id_unique` (`user_id`,`car_id`),
  CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_car_id_foreign` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `car_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('pending','confirmed','paid','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total` DECIMAL(15,2) NOT NULL,
  `payment_method` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_car_id_foreign` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `method` ENUM('cash','transfer','va') NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL,
  `proof_file` VARCHAR(255) NULL,
  `status` ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `verified_by` BIGINT UNSIGNED NULL,
  `verified_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `test_drives` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `car_id` BIGINT UNSIGNED NOT NULL,
  `booking_date` DATE NOT NULL,
  `booking_time` TIME NOT NULL,
  `status` ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `test_drives_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `test_drives_car_id_foreign` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `offers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `car_id` BIGINT UNSIGNED NOT NULL,
  `offer_price` DECIMAL(15,2) NOT NULL,
  `status` ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `offers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offers_car_id_foreign` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed minimal data
INSERT INTO `users` (`id`,`name`,`email`,`phone`,`role`,`password`,`created_at`,`updated_at`) VALUES
(1,'Owner Maharani','owner@maharani.com','081200000001','owner','$2y$10$/9ElCbtFg.3xzSBMuyDQw.qEB9KJ4fXUamXAQVnKN8nwxRD6pjL.q',NOW(),NOW()),
(2,'Supervisor Maharani','supervisor@maharani.com','081200000002','supervisor','$2y$10$hwdmpZvLOqS5oVklaDtrNOjT3EbL2wt7pl1f4b8o22wCyZe49t/bS',NOW(),NOW()),
(3,'Marketing Maharani','marketing@maharani.com','081200000003','marketing','$2y$10$xYSX0ysXaeXs7OTWICQxZOXcSU79aX7vex2Bgj9xnUHPEcFW5UY6S',NOW(),NOW()),
(4,'Customer One','customer1@gmail.com','081200000004','customer','$2y$10$W5Shye.RhMYdFdBq38waTOwLzTcgHoTR6Xc2r0ntsLP8dvQal/Fqi',NOW(),NOW()),
(5,'Customer Two','customer2@gmail.com','081200000005','customer','$2y$10$W5Shye.RhMYdFdBq38waTOwLzTcgHoTR6Xc2r0ntsLP8dvQal/Fqi',NOW(),NOW());

INSERT INTO `cars` (`id`,`kode_unit`,`merk`,`tipe`,`tahun`,`harga`,`kilometer`,`transmisi`,`warna`,`bahan_bakar`,`status`,`deskripsi`,`created_by`,`created_at`,`updated_at`) VALUES
(1,'MM-AVZ-2019','Toyota','Avanza G',2019,185000000.00,42000,'Automatic','White','Bensin','available','Unit keluarga terawat, servis berkala.',2,NOW(),NOW()),
(2,'MM-HRV-2021','Honda','HR-V 1.5 E',2021,295000000.00,18500,'CVT','Gray','Bensin','reserved','Interior bersih, pajak panjang.',2,NOW(),NOW()),
(3,'MM-CAM-2022','Toyota','Camry 2.5 V Hybrid',2022,545000000.00,12450,'Automatic','White Pearl','Hybrid','available','Hybrid premium, low mileage.',2,NOW(),NOW());

INSERT INTO `orders` (`id`,`user_id`,`car_id`,`status`,`total`,`payment_method`,`notes`,`created_at`,`updated_at`) VALUES
(1,4,1,'paid',185000000.00,'transfer','DP 20%',NOW(),NOW());

INSERT INTO `payments` (`id`,`order_id`,`method`,`amount`,`status`,`verified_by`,`verified_at`,`created_at`,`updated_at`) VALUES
(1,1,'transfer',185000000.00,'verified',2,NOW(),NOW(),NOW());

INSERT INTO `favorites` (`id`,`user_id`,`car_id`,`created_at`,`updated_at`) VALUES
(1,4,3,NOW(),NOW());

INSERT INTO `test_drives` (`id`,`user_id`,`car_id`,`booking_date`,`booking_time`,`status`,`notes`,`created_at`,`updated_at`) VALUES
(1,5,3,DATE_ADD(CURDATE(), INTERVAL 2 DAY),'10:00:00','pending',NULL,NOW(),NOW());

INSERT INTO `offers` (`id`,`user_id`,`car_id`,`offer_price`,`status`,`notes`,`created_at`,`updated_at`) VALUES
(1,5,2,285000000.00,'pending',NULL,NOW(),NOW());

SET FOREIGN_KEY_CHECKS=1;
