-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2025 at 05:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentalcloth`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_product`
--

CREATE TABLE `add_product` (
  `product_id` int(10) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` bigint(100) NOT NULL,
  `product_image1` varchar(100) NOT NULL,
  `size` char(50) NOT NULL,
  `qty` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_product`
--

INSERT INTO `add_product` (`product_id`, `product_name`, `product_price`, `product_image1`, `size`, `qty`) VALUES
(11, 'anarkali', 10000, 'anarkali.png', 'XL', 1),
(12, 'croptop1', 15000, 'croptop.png', 'M', 2),
(13, 'croptop2', 5000, 'croptop2.png', 'XXL', 1),
(14, 'gown1', 8000, 'gown1.png', 'S', 2),
(15, 'gown2', 8000, 'gown2.png', 'L', 3),
(16, 'indow1', 12000, 'indow1.png', 'L', 2),
(17, 'indow2', 10000, 'indow2.png', 'XL', 2),
(18, 'kurta1', 1000, 'kurta1.png', 'S', 1),
(19, 'kurta2', 2000, 'kurta2.png', 'M', 2),
(20, 'lehenga1', 7000, 'lehenga1.png', 'L', 1),
(21, 'lehenga2', 8500, 'lehenga2.png', 'S', 2),
(22, 'saree1', 11000, 'saree1.png', 'FREE', 3),
(23, 'suit1', 15000, 'suit1.png', 'XL', 2),
(24, 'suit2', 9000, 'suit2.png', 'S', 3),
(25, 'western1', 6000, 'western1.png', 'M', 2),
(26, 'western2', 7500, 'western2.png', 'L', 1),
(11, 'anarkali', 10000, 'anarkali.png', 'XL', 1),
(12, 'croptop1', 15000, 'croptop.png', 'M', 2),
(13, 'croptop2', 5000, 'croptop2.png', 'XXL', 1),
(14, 'gown1', 8000, 'gown1.png', 'S', 2),
(15, 'gown2', 8000, 'gown2.png', 'L', 3),
(16, 'indow1', 12000, 'indow1.png', 'L', 2),
(17, 'indow2', 10000, 'indow2.png', 'XL', 2),
(18, 'kurta1', 1000, 'kurta1.png', 'S', 1),
(19, 'kurta2', 2000, 'kurta2.png', 'M', 2),
(20, 'lehenga1', 7000, 'lehenga1.png', 'L', 1),
(21, 'lehenga2', 8500, 'lehenga2.png', 'S', 2),
(22, 'saree1', 11000, 'saree1.png', 'FREE', 3),
(23, 'suit1', 15000, 'suit1.png', 'XL', 2),
(24, 'suit2', 9000, 'suit2.png', 'S', 3),
(25, 'western1', 6000, 'western1.png', 'M', 2),
(26, 'western2', 7500, 'western2.png', 'L', 1);

-- --------------------------------------------------------

--
-- Table structure for table `add_to_cart`
--

CREATE TABLE `add_to_cart` (
  `User_Id` int(10) NOT NULL,
  `product_Id` bigint(10) NOT NULL,
  `product_price` bigint(10) NOT NULL,
  `product_image` varchar(70) NOT NULL,
  `product_Name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `email` varchar(200) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `fullname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `rental_date` date NOT NULL,
  `confirm_size` varchar(50) NOT NULL,
  `return_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `name`, `address`, `phone`, `rental_date`, `confirm_size`, `return_date`) VALUES
(1, 'test', '[value-3]', '[value-4]', '0000-00-00', '[value-6]', '0000-00-00'),
(1, 'test', '[value-3]', '[value-4]', '0000-00-00', '[value-6]', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `product_order`
--

CREATE TABLE `product_order` (
  `User_Id` int(10) NOT NULL,
  `product_Id` int(10) NOT NULL,
  `product_price` int(10) NOT NULL,
  `product_image` varchar(70) NOT NULL,
  `Order_Id` varchar(30) NOT NULL,
  `Order_Date` date NOT NULL,
  `Order_Time` time NOT NULL,
  `product_Name` varchar(60) NOT NULL,
  `Payment_Method` varchar(40) NOT NULL,
  `Total_price` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `password` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`password`, `name`, `email`) VALUES
(0, 'admin', 'admin@gmail.com'),
(1, 'dhvani butani', 'dhvani@gmail.com'),
(0, 'admin', 'admin@gmail.com'),
(1, 'dhvani butani', 'dhvani@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE `user_data` (
  `User_Id` int(10) NOT NULL,
  `password` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Mobile_No` bigint(10) NOT NULL,
  `First_Name` varchar(30) NOT NULL,
  `Last_Name` varchar(30) NOT NULL,
  `Address` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_data`
--

INSERT INTO `user_data` (`User_Id`, `password`, `Email`, `Mobile_No`, `First_Name`, `Last_Name`, `Address`) VALUES
(10, '123456', 'ab@gmail.com', 9090909090, 'tanvi', 'vagadiya', 'dhoraji'),
(12, 'abc', 'xyz@gmail.com', 8080808080, 'dhvani', 'butani', 'rajkot'),
(10, '123456', 'ab@gmail.com', 9090909090, 'tanvi', 'vagadiya', 'dhoraji'),
(12, 'abc', 'xyz@gmail.com', 8080808080, 'dhvani', 'butani', 'rajkot'),
(10, '123456', 'ab@gmail.com', 9090909090, 'tanvi', 'vagadiya', 'dhoraji'),
(12, 'abc', 'xyz@gmail.com', 8080808080, 'dhvani', 'butani', 'rajkot'),
(10, '123456', 'ab@gmail.com', 9090909090, 'tanvi', 'vagadiya', 'dhoraji'),
(12, 'abc', 'xyz@gmail.com', 8080808080, 'dhvani', 'butani', 'rajkot');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
