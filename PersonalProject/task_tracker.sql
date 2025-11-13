-- Create a fresh database
CREATE DATABASE `tasktracker` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tasktracker`;

-- Users table
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','user') NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tasks table
CREATE TABLE `tasks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `category` VARCHAR(150),
  `priority` ENUM('Low','Medium','High') DEFAULT 'Medium',
  `status` ENUM('Pending','Completed') DEFAULT 'Pending',
  `approved` ENUM('Pending','Accepted','Declined') DEFAULT 'Pending',
  `due_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin and user
INSERT INTO `users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@example.com', '$2y$10$KIX8j4I6d2Pq6AxgCj1Y4O8s5OCcD8d9HcZsG3gE7YfV5ZrIYOy6', 'admin'), -- password: admin123
('user', 'user@example.com', '$2y$10$X1WbSxC8bVd.1vR4mLv3leEbc1uPt5B2jX.bF1MJvlhLxO/Bf2Oqa', 'user');     -- password: user123
