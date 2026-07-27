-- Smart Student Panic Button and Emergency Response System
-- Database Export / Dump
-- Target: MySQL / MariaDB

CREATE DATABASE IF NOT EXISTS `campus_emergency_system`;
USE `campus_emergency_system`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(255) NOT NULL, -- NDRRMO / Clinic
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `devices`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `devices`;
CREATE TABLE `devices` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `device_code` VARCHAR(255) UNIQUE NOT NULL,
  `building` VARCHAR(255) NOT NULL,
  `floor` VARCHAR(255) NULL,
  `room` VARCHAR(255) NULL,
  `latitude` DECIMAL(10, 7) NULL,
  `longitude` DECIMAL(10, 7) NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'active',
  `last_seen` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `activity_logs`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `activity` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_activity_logs_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `incidents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `incidents`;
CREATE TABLE `incidents` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `device_id` BIGINT UNSIGNED NOT NULL,
  `emergency_type` VARCHAR(255) NOT NULL,
  `reported_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
  `remarks` TEXT NULL,
  `resolved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_incidents_device_id` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `notifications`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `incident_id` BIGINT UNSIGNED NOT NULL,
  `recipient` VARCHAR(255) NOT NULL,
  `channel` VARCHAR(255) NOT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_notifications_incident_id` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `patient_records`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `patient_records`;
CREATE TABLE `patient_records` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `incident_id` BIGINT UNSIGNED NULL,
  `patient_name` VARCHAR(255) NULL,
  `student_id` VARCHAR(255) NULL,
  `injury_details` TEXT NULL,
  `treatment_given` TEXT NULL,
  `status` VARCHAR(255) NOT NULL DEFAULT 'Admitted', -- Admitted, Discharged, Transferred
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_patient_records_incident_id` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Dumping data for table `users`
-- --------------------------------------------------------
INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'NDRRMO Administrator', 'ndrrmo', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'NDRRMO', 'active', NOW(), NOW()),
(2, 'Clinic Administrator', 'clinic', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Clinic', 'active', NOW(), NOW());

-- Note: The password hash above is the bcrypt representation of the password 'password'.

-- --------------------------------------------------------
-- Dumping data for table `devices`
-- --------------------------------------------------------
INSERT INTO `devices` (`id`, `device_code`, `building`, `floor`, `room`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'GYM-001', 'Gymnasium', '1st Floor', 'Main Hall', 8.1234567, 123.1234567, 'active', NOW(), NOW()),
(2, 'ENG-001', 'Engineering Building', '2nd Floor', 'Room 203', 8.1235567, 123.1236567, 'active', NOW(), NOW()),
(3, 'LIB-001', 'Library', '1st Floor', 'Reading Area', 8.1233567, 123.1232567, 'active', NOW(), NOW());
