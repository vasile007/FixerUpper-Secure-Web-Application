-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 25, 2026 at 02:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fixerupper_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fulfilment_method` varchar(20) NOT NULL DEFAULT 'collection',
  `customer_name` varchar(150) NOT NULL DEFAULT '',
  `address_line1` varchar(255) NOT NULL DEFAULT '',
  `address_line2` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `postcode` varchar(20) NOT NULL DEFAULT '',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fulfilment_method`, `customer_name`, `address_line1`, `address_line2`, `city`, `postcode`, `order_date`) VALUES
(1, 1, 'collection', '', '', '', '', '', '2026-06-22 09:22:22'),
(2, 1, 'collection', '', '', '', '', '', '2026-06-22 09:51:20'),
(3, 1, 'collection', '', '', '', '', '', '2026-06-23 09:07:16'),
(4, 1, 'delivery', '', '', '', '', '', '2026-06-23 09:16:18'),
(5, 1, 'delivery', '', '', '', '', '', '2026-06-23 10:16:02'),
(6, 1, 'delivery', 'John Doe', '4,9 New Street', '', 'Leicester', 'LE1 5NR', '2026-06-25 10:57:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(1, 1, 9, 1),
(2, 2, 3, 1),
(3, 3, 8, 2),
(4, 4, 2, 1),
(5, 5, 1, 1),
(6, 6, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`) VALUES
(1, 'Cordless Drill', 'Versatile cordless drill for drilling and screwdriving tasks around the home and workshop.', 79.99, 'assets/images/cordless-drill.svg'),
(2, 'Hammer', 'Balanced steel claw hammer designed for general construction and DIY work.', 14.99, 'assets/images/hammer.svg'),
(3, 'Screwdriver Set', 'Precision and standard screwdriver set with a range of flathead and Phillips sizes.', 22.50, 'assets/images/screwdriver-set.svg'),
(4, 'Circular Saw', 'High-performance circular saw for clean cutting through timber, sheet materials, and more.', 129.99, 'assets/images/circular-saw.svg'),
(5, 'Sander', 'Electric detail sander with dust collection for smooth finishing on wood surfaces.', 64.95, 'assets/images/sander.svg'),
(6, 'Tool Kit', 'Comprehensive tool kit containing common hand tools for household repairs and maintenance.', 89.00, 'assets/images/tool-kit.svg'),
(7, 'Power Drill', 'Mains-powered drill with variable speed control and reliable torque for workshop tasks.', 99.95, 'assets/images/power-drill.svg'),
(8, 'Angle Grinder', 'Compact angle grinder suitable for cutting, grinding, and polishing metal surfaces.', 74.50, 'assets/images/angle-grinder.svg'),
(9, 'Measuring Tape', 'Durable retractable measuring tape with an easy-read imperial and metric scale.', 12.75, 'assets/images/measuring-tape.svg'),
(10, 'Wrench Set', 'Multi-size wrench set for assembly, plumbing, and mechanical maintenance work.', 34.99, 'assets/images/wrench-set.svg'),
(11, 'Electric Screwdriver', 'Rechargeable electric screwdriver for fast, accurate fastening with less effort.', 39.95, 'assets/images/electric-screwdriver.svg'),
(12, 'Safety Equipment', 'Essential safety equipment bundle including eye protection, gloves, and ear defenders.', 27.99, 'assets/images/safety-equipment.svg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `created_at`) VALUES
(1, 'John Doe', 'jondoe@gmail.com', '$2y$10$0T5Iiw1y00qMjbu9FB0bGuLXUF1daNyQS7XR1GMJ38CNpztZCAuSu', '2026-06-22 09:22:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
