-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 15, 2025 at 02:44 AM
-- Server version: 8.0.41
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int NOT NULL,
  `job_id` int NOT NULL,
  `student_id` int NOT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `student_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'pending', '2025-04-14 03:58:04', '2025-04-14 07:21:05'),
(2, 2, 3, 'rejected', '2025-04-14 03:58:04', '2025-04-14 04:05:57'),
(3, 1, 4, 'pending', '2025-04-14 03:58:04', '2025-04-14 03:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `address` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `user_id`, `name`, `description`, `address`, `industry`, `email`, `phone`, `website`, `created_at`, `updated_at`) VALUES
(1, 2, 'Công Ty Công Nghệ ABC', 'Công ty chuyên về phát triển phần mềm', 'Hà Nội', 'IT', 'contact@abc.com', '0123456789', 'https://abc.com', '2025-04-14 03:58:04', '2025-04-14 03:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int NOT NULL,
  `company_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `requirements` text,
  `salary` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `type` enum('full_time','part_time_morning','part_time_afternoon','flexible') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `company_id`, `title`, `description`, `requirements`, `salary`, `location`, `category`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lập trình viên PHP', 'Tuyển dụng lập trình viên PHP có kinh nghiệm', 'Kinh nghiệm 1 năm, biết PHP, MySQL', '10000000 - 15000000', 'Hà Nội', 'IT', 'full_time', '2025-04-14 03:58:04', '2025-04-14 06:30:32'),
(2, 1, 'Thiết kế đồ họa', 'Tuyển dụng nhân viên thiết kế đồ họa', 'Thành thạo Photoshop, Illustrator', '8000000 - 12000000', 'Hà Nội', 'Design', 'part_time_morning', '2025-04-14 03:58:04', '2025-04-14 06:33:12'),
(4, 1, 'Lập Trình', 'a', 'a', '120000000', 'Phú Thọ', NULL, 'full_time', '2025-04-14 04:25:26', '2025-04-14 04:25:26'),
(5, 1, 'Design', 'a', 'a', '12', 'Phú Thọ', 'IT', 'full_time', '2025-04-14 04:32:59', '2025-04-14 04:32:59');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `university` varchar(255) NOT NULL,
  `major` varchar(255) NOT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `graduation_year` int DEFAULT NULL,
  `work_time_preference` enum('full_time','part_time_morning','part_time_afternoon','flexible') NOT NULL,
  `desired_position` varchar(255) DEFAULT NULL,
  `skills` text,
  `experience` text,
  `achievements` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `university`, `major`, `gpa`, `graduation_year`, `work_time_preference`, `desired_position`, `skills`, `experience`, `achievements`, `created_at`, `updated_at`) VALUES
(1, 3, 'Đại học Bách Khoa Hà Nội', 'Công nghệ thông tin', '3.50', 2024, 'full_time', 'Lập trình viên', 'PHP, MySQL, JavaScript, HTML, CSS', 'Thực tập tại FPT Software', 'Giải nhì Olympic Tin học sinh viên', '2025-04-14 03:58:04', '2025-04-14 03:58:04'),
(2, 4, 'Đại học Kinh tế Quốc dân', 'Quản trị kinh doanh', '3.20', 2024, 'part_time_morning', 'Nhân viên Marketing', 'Content writing, Social media, SEO', 'Thực tập tại Viettel', 'Giải ba cuộc thi Marketing sinh viên', '2025-04-14 03:58:04', '2025-04-14 03:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','company','student') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-04-14 03:58:04'),
(2, 'Công Ty A', 'company1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'company', '2025-04-14 03:58:04'),
(3, 'Sinh Viên 1', 'student1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2025-04-14 03:58:04'),
(4, 'Sinh Viên 2', 'student2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', '2025-04-14 03:58:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
