-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 26 فبراير 2026 الساعة 23:18
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sweet`
--

-- --------------------------------------------------------

--
-- بنية الجدول `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `total_price` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `cart`
--

INSERT INTO `cart` (`id`, `itemName`, `price`, `image`, `quantity`, `catName`, `email`, `total_price`) VALUES
(2, 'BBQ Chicken Pizza', 1000, 'bbq-pizza.jpg', 1, 'Pizza', 'zidnan@gmail.com', '1000'),
(3, 'Strawberry Mocktail', 550, 'strawberry-drink.png', 2, 'Beverage', 'zidnan@gmail.com', '1100'),
(140, 'عصير فراولة', 2500, 'milkshake-smoothie-strawberry-juice-chocolate-milk-juice-f6d6888a912cd2aea7f0e2f6643d4434.png', 1, 'قسم العصائر', 'admin@gmail.com', '2500'),
(141, 'جاتو مناسبات', 25000, 'IMG_20260103_182811_857.jpg', 1, 'قسم الجاتو', 'admin@gmail.com', '25000');

-- --------------------------------------------------------

--
-- بنية الجدول `menucategory`
--

CREATE TABLE `menucategory` (
  `catId` int(11) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `menucategory`
--

INSERT INTO `menucategory` (`catId`, `catName`, `dateCreated`) VALUES
(15, 'قسم الجاتو', '2026-02-14 08:03:07'),
(16, 'قسم العصائر', '2026-02-21 21:59:02'),
(17, 'قسم المعجنات', '2026-02-22 00:31:03'),
(18, 'قسم المقبلات', '2026-02-22 00:31:55');

-- --------------------------------------------------------

--
-- بنية الجدول `menuitem`
--

CREATE TABLE `menuitem` (
  `itemId` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `catName` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `status` enum('Available','Unavailable') NOT NULL DEFAULT 'Available',
  `description` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedDate` datetime NOT NULL,
  `is_popular` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `menuitem`
--

INSERT INTO `menuitem` (`itemId`, `itemName`, `catName`, `price`, `status`, `description`, `image`, `dateCreated`, `updatedDate`, `is_popular`) VALUES
(37, 'بسبوسه مكسرات ', 'قسم المقبلات', '12000', 'Available', 'بسبوسه مكسرات محشيه بلقشطه', 'IMG_20260103_182750_135.jpg', '2026-02-14 07:53:35', '2026-02-14 20:53:35', 1),
(38, 'كنافه', 'قسم المقبلات', '10000', 'Available', 'كنافه', 'IMG_20260103_182755_778.jpg', '2026-02-14 07:54:36', '2026-02-14 20:54:36', 1),
(39, 'جاتو', 'قسم الجاتو', '22000', 'Available', 'جاتو اوريو', 'IMG_20260103_182817_552.jpg', '2026-02-14 08:03:34', '2026-02-14 21:03:34', 1),
(40, 'هذه تورتة (كيكة) كلاسيكية بتصميم أنيق وهادئ.', 'قسم الجاتو', '15000', 'Available', 'التصميم العام: كيكة دائرية مكونة من طبقة واحدة مرتفعة، مغطاة بالكامل بكريمة بيضاء ناعمة (Frosting).  التزيين العلوي: * تتوسط الكيكة شمعة واحدة طويلة باللون الأزرق الفاتح مشتعلة، مما يعطي إيحاءً بالاحتفال.  باقة من الزهور المصنوعة من الكريمة باللون الأزرق ', 'birthday-cake-chocolate-cake-wedding-cake-sponge-cake-frosting-icing-beautiful-birthday-cake-png-a1fb6a8c52978d389277caf90e65d80e.png', '2026-02-21 21:55:54', '2026-02-22 00:55:54', 1),
(41, 'عصير بطيخ', 'قسم العصائر', '1500', 'Available', 'عصير بطيخ طازج طبيعي 100%', 'fruit-citrullus-lanatus-food-watermelon-auglis-juice-efc4a4e63755832366de6839c74edc99.png', '2026-02-21 21:59:38', '2026-02-22 00:59:38', 1),
(42, 'جاتو مناسبات', 'قسم الجاتو', '25000', 'Available', 'جاتو تحفه', 'IMG_20260103_182811_857.jpg', '2026-02-22 00:33:36', '2026-02-22 03:33:36', 1),
(43, 'عصير منجا', 'قسم العصائر', '2000', 'Available', 'عصير منجا طازج', 'healthy-useful-and-nutritious-drinks-thumbnail-186323.webp', '2026-02-22 00:34:59', '2026-02-22 03:34:59', 1),
(44, 'عصير فراولة', 'قسم العصائر', '2500', 'Available', 'عصير فراوله', 'milkshake-smoothie-strawberry-juice-chocolate-milk-juice-f6d6888a912cd2aea7f0e2f6643d4434.png', '2026-02-22 00:37:22', '2026-02-22 03:37:22', 0),
(45, 'معجنات', 'قسم المعجنات', '10000', 'Available', 'معجنات مشكل', 'bakery-bread-pastry-baking-bread-499de46097fff1d409990a5412e915af.png', '2026-02-22 00:54:14', '2026-02-22 03:54:14', 0),
(46, 'كوكيز', 'قسم المعجنات', '2200', 'Available', 'كوكيز محشي بشكلاته', 'chocolate-chip-cookie-biscuits-chips-ahoy-nabisco-cookies-82d884d2cb3abfd8187d81b291290f9f.png', '2026-02-22 00:55:50', '2026-02-22 03:55:50', 0),
(47, 'كرواسون', 'قسم المعجنات', '250', 'Available', 'كرواسون بالجبنه', 'croissant-doughnut-breakfast-bakery-pain-au-chocolat-basket-of-croissants-8c64e56ce3239c1083e9e3b06639c1bd.png', '2026-02-22 00:57:10', '2026-02-22 03:57:10', 0),
(48, 'عصيرافوكادو ', 'قسم العصائر', '2500', 'Available', 'عصير افوكادو طازج', 'IMG_20260103_182805_762.jpg', '2026-02-22 00:58:30', '2026-02-22 03:58:30', 0),
(49, 'جاتو مذهب', 'قسم الجاتو', '16000', 'Available', 'جاتو بشكلاته الذهبيه', '5bfb7554a930f-893b62f181b801bbf2c67dcf332dc37d.png', '2026-02-22 00:59:43', '2026-02-22 03:59:43', 0),
(50, 'عصير تفاح', 'قسم العصائر', '1800', 'Available', 'عصير تفاح الاخضر', 'apple-juice-smoothie-cocktail-apple-pie-green-apple-juice-899c789269a4950915e2df7c625148c6.png', '2026-02-22 01:00:28', '2026-02-22 04:00:28', 0),
(51, 'كب كيك', 'قسم المقبلات', '500', 'Available', 'كب كيك بالفروله', 'cupcake-icing-peanut-butter-cup-red-velvet-cake-cupcake-0566be047654cf293534cecbe5352ce0.png', '2026-02-22 01:01:38', '2026-02-22 04:01:38', 0),
(52, 'جاتو مناسبات', 'قسم الجاتو', '26000', 'Available', 'جاتو اعراس', 'wedding-cake-birthday-cake-cream-dripping-cake-rose-cake-62caf8115b26ed356ec49cb2a6e5b9b8.png', '2026-02-22 01:02:57', '2026-02-22 04:02:57', 0),
(53, 'معجنات بيتي فور', 'قسم المعجنات', '1700', 'Available', 'معجنات بيتي فور الذيذه', 'bakery-cookie-biscuit-baking-cake-cookies-pattern-dessert-biscuit-4b7c9b4e8ba4c744eb9312876025b568.png', '2026-02-22 22:28:57', '2026-02-23 01:28:57', 0),
(54, 'دونت', 'قسم المقبلات', '3000', 'Available', 'كعك دونت الشهيه المحشيه بانواع الشكولا', 'tea-coffee-donuts-frosting-icing-ciambella-donuts-7a87deb8986ba6eb4df1f607d832a9ef.png', '2026-02-22 22:30:49', '2026-02-23 01:30:49', 0),
(55, 'كب كيك', 'قسم المقبلات', '500', 'Available', 'كب كيك فنيليا', 'cupcake-frosting-icing-chocolate-cake-birthday-cake-profiterole-cupcakes-fe0d493048f7ba5ce4fd15e123b72fe2.png', '2026-02-23 00:37:05', '2026-02-23 03:37:05', 0);

-- --------------------------------------------------------

--
-- بنية الجدول `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `address` varchar(200) NOT NULL,
  `pmode` enum('Cash','Card','Takeaway') NOT NULL DEFAULT 'Cash',
  `payment_status` enum('Pending','Successful','Rejected') NOT NULL DEFAULT 'Pending',
  `sub_total` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `delivery_type` varchar(50) NOT NULL DEFAULT 'pickup',
  `area_distance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_zone_id` int(11) DEFAULT NULL,
  `delivery_distance` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` enum('Pending','Completed','Cancelled','Processing','On the way','') DEFAULT 'Pending',
  `delivery_id` int(11) DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `orders`
--

INSERT INTO `orders` (`order_id`, `email`, `firstName`, `lastName`, `phone`, `address`, `pmode`, `payment_status`, `sub_total`, `grand_total`, `delivery_fee`, `delivery_type`, `area_distance`, `delivery_zone_id`, `delivery_distance`, `order_date`, `order_status`, `delivery_id`, `cancel_reason`, `note`) VALUES
(54, 'preethi@gmail.com', 'Preethi', 'Suresh', '9999999999', 'Galle Road', 'Cash', 'Successful', 1910.00, 2040.00, 0.00, 'pickup', 0.00, NULL, NULL, '2024-08-11 18:00:04', '', NULL, '', 'Add extra cheese'),
(55, 'zidnan@gmail.com', 'Zidnan', 'Ahamad', '2222222222', 'Kolonnawa', 'Cash', 'Pending', 7420.00, 7550.00, 0.00, 'pickup', 0.00, NULL, NULL, '2024-08-10 18:02:26', 'On the way', NULL, '', 'Please make the Burger extra spicy'),
(56, 'zidnan@gmail.com', 'Mohamed', 'Muhadh', '0000000000', 'Kolonnawa', 'Takeaway', 'Successful', 1150.00, 1150.00, 0.00, 'pickup', 0.00, NULL, NULL, '2024-08-11 18:04:16', 'Completed', NULL, '', ''),
(57, 'jhon@gmail.com', 'Jhon', 'Paul', '7777777777', 'Colombo 15', 'Takeaway', 'Successful', 5720.00, 5720.00, 0.00, 'pickup', 0.00, NULL, NULL, '2024-08-08 18:05:26', 'Completed', NULL, '', ''),
(58, 'zidnan@gmail.com', 'Zidnan', 'Ahamad', '4444444444', 'Colombo 12', 'Takeaway', 'Pending', 2700.00, 2700.00, 0.00, 'pickup', 0.00, NULL, NULL, '2024-08-10 20:12:14', 'Cancelled', NULL, 'Waiting time is too long.', ''),
(59, 'asna@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'ببببب', 'Takeaway', 'Successful', 22000.00, 22130.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-14 20:04:41', 'Completed', NULL, '', 'بببب'),
(60, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'hffhh', 'Cash', 'Successful', 800.00, 930.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-21 22:14:43', 'Completed', NULL, '', 'iyhyhiuhy'),
(61, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'نننة', 'Takeaway', 'Successful', 1500.00, 1630.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-22 00:08:36', 'Completed', NULL, '', 'منمنمن'),
(62, 'asna@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'ننىتنى', 'Takeaway', 'Pending', 30000.00, 30130.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-22 00:47:12', 'Pending', NULL, NULL, 'نىننى'),
(63, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'يابيا', 'Takeaway', 'Successful', 2000.00, 2130.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-22 23:45:36', 'Completed', 3, NULL, 'قثغق'),
(64, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'شارع حده', 'Takeaway', 'Successful', 250.00, 380.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-23 02:35:35', 'Completed', 3, NULL, 'اريد تغليف'),
(65, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'شارع حده', 'Takeaway', 'Pending', 500.00, 630.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-23 02:37:53', '', 3, NULL, 'توصيل السريع'),
(66, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'نتنتن', 'Takeaway', 'Successful', 25000.00, 25130.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-24 00:09:38', '', NULL, '', 'منتسنيت'),
(74, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'لتالتل', 'Takeaway', 'Successful', 2200.00, 2330.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-24 03:20:02', '', NULL, NULL, 'نتنت'),
(75, 'anas1@gmail.com', 'ِAnas', 'zerooo', '0000000000', 'جوار فندق ', 'Takeaway', 'Successful', 25000.00, 25400.00, 0.00, 'pickup', 0.00, NULL, NULL, '2026-02-24 23:04:05', 'Completed', NULL, '', 'توصيل السريع\n[معلومات التوصيل: المسافة=2 كم، أجرة التوصيل=400 ريال]'),
(76, 'anas1@gmail.com', 'Anas', 'zerooo', '0000000000', 'بينون', 'Takeaway', 'Successful', 23700.00, 25300.00, 1600.00, '0', 8.00, NULL, NULL, '2026-02-25 02:29:54', '', NULL, NULL, 'توصيل السريع\n[معلومات التوصيل: المسافة=8 كم، أجرة التوصيل=1600 ريال]'),
(77, 'anas1@gmail.com', 'ِAnas', 'zerooo', '0000000000', 'حي شميلة', 'Takeaway', 'Successful', 2500.00, 3000.00, 500.00, '0', 2.50, NULL, NULL, '2026-02-25 02:53:23', '', NULL, NULL, 'مع الثلج\n[معلومات التوصيل: المسافة=2.5 كم، أجرة التوصيل=500 ريال]'),
(78, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'استلام من المتجر', 'Takeaway', 'Pending', 2500.00, 2500.00, 0.00, '0', 0.00, NULL, NULL, '2026-02-25 03:20:10', '', 3, NULL, 'ييي'),
(79, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'حاشد', 'Takeaway', 'Successful', 500.00, 1100.00, 600.00, '0', 3.00, NULL, NULL, '2026-02-25 03:20:53', '', NULL, NULL, 'يييي\n[معلومات التوصيل: المسافة=3 كم، أجرة التوصيل=600 ريال]'),
(80, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'استلام من المتجر', 'Takeaway', 'Pending', 25000.00, 25600.00, 0.00, '0', 0.00, NULL, NULL, '2026-02-26 03:06:09', '', 3, NULL, 'تنتن'),
(81, 'anas1@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'استلام من المتجر', 'Takeaway', 'Pending', 16000.00, 16600.00, 0.00, '0', 0.00, NULL, NULL, '2026-02-26 03:06:41', '', 3, NULL, ''),
(82, 'asna@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'حاشد', 'Takeaway', 'Successful', 2000.00, 2600.00, 600.00, '0', 3.00, NULL, NULL, '2026-02-26 04:03:43', 'On the way', NULL, '', 'نتنتن\n[معلومات التوصيل: المسافة=3 كم، أجرة التوصيل=600 ريال]'),
(83, 'asna@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'حاشد', 'Takeaway', 'Pending', 25000.00, 25600.00, 600.00, '0', 3.00, NULL, NULL, '2026-02-26 04:24:25', '', 3, NULL, 'تتت\n[معلومات التوصيل: المسافة=3 كم، أجرة التوصيل=600 ريال]'),
(84, 'asna@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'حاشد', 'Takeaway', 'Pending', 1700.00, 2300.00, 600.00, '0', 3.00, NULL, NULL, '2026-02-26 04:29:18', 'Cancelled', 3, 'حر', 'ناتن\n[معلومات التوصيل: المسافة=3 كم، أجرة التوصيل=600 ريال]'),
(85, 'asna@gmail.com', 'BADER', 'ALSHAMMARI', '0000000000', 'حاشد', 'Takeaway', 'Successful', 500.00, 1100.00, 600.00, '0', 3.00, NULL, NULL, '2026-02-26 04:41:59', 'Completed', 3, NULL, '\n[معلومات التوصيل: المسافة=3 كم، أجرة التوصيل=600 ريال]'),
(86, 'anas1@gmail.com', 'Anas', 'zeroooo', '0000000000', 'حديقة', 'Takeaway', 'Successful', 16000.00, 16800.00, 800.00, '0', 4.00, NULL, NULL, '2026-02-26 22:14:23', 'Completed', 3, NULL, 'توصيل السريع\n[معلومات التوصيل: المسافة=4 كم، أجرة التوصيل=800 ريال]');

-- --------------------------------------------------------

--
-- بنية الجدول `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `itemName`, `image`, `quantity`, `price`, `total_price`) VALUES
(122, 54, 'Garlic Bread', 'garlic-bread.avif', 1, 350, 350.00),
(123, 54, 'French Fries', 'fries.jpg', 1, 760, 760.00),
(124, 54, 'Cheese Pizza', 'cheese-pizza.jpg', 1, 800, 800.00),
(125, 55, 'Dragon Fruit Mojito', 'Dragon-fruit-drink.png', 1, 760, 760.00),
(126, 55, 'BBQ Chicken Burger', 'bbq-burger.jpeg', 3, 1900, 5700.00),
(127, 55, 'Chicken Wing', 'chicken-wing.avif', 2, 480, 960.00),
(128, 56, 'Garlic Bread', 'garlic-bread.avif', 1, 350, 350.00),
(129, 56, 'Cheese Pizza', 'cheese-pizza.jpg', 1, 800, 800.00),
(130, 57, 'French Fries', 'fries.jpg', 2, 760, 1520.00),
(131, 57, 'Firebird Burger', 'firebird-burger.jpeg', 2, 2100, 4200.00),
(132, 58, 'Garlic Bread', 'garlic-bread.avif', 3, 350, 1050.00),
(133, 58, 'Strawberry Mocktail', 'strawberry-drink.png', 3, 550, 1650.00),
(134, 59, 'جاتو', 'IMG_20260103_182817_552.jpg', 1, 22000, 22000.00),
(135, 60, 'Veggie Supreme Pizza', 'veggie-pizza.jpg', 1, 800, 800.00),
(136, 61, 'عصير بطيخ', 'fruit-citrullus-lanatus-food-watermelon-auglis-juice-efc4a4e63755832366de6839c74edc99.png', 1, 1500, 1500.00),
(137, 62, 'هذه تورتة (كيكة) كلاسيكية بتصميم أنيق وهادئ.', 'birthday-cake-chocolate-cake-wedding-cake-sponge-cake-frosting-icing-beautiful-birthday-cake-png-a1fb6a8c52978d389277caf90e65d80e.png', 2, 15000, 30000.00),
(138, 63, 'عصير منجا', 'healthy-useful-and-nutritious-drinks-thumbnail-186323.webp', 1, 2000, 2000.00),
(139, 64, 'كرواسون', 'croissant-doughnut-breakfast-bakery-pain-au-chocolat-basket-of-croissants-8c64e56ce3239c1083e9e3b06639c1bd.png', 1, 250, 250.00),
(140, 65, 'كب كيك', 'cupcake-frosting-icing-chocolate-cake-birthday-cake-profiterole-cupcakes-fe0d493048f7ba5ce4fd15e123b72fe2.png', 1, 500, 500.00),
(141, 66, 'جاتو مناسبات', 'IMG_20260103_182811_857.jpg', 1, 25000, 25000.00),
(149, 74, 'كوكيز', 'chocolate-chip-cookie-biscuits-chips-ahoy-nabisco-cookies-82d884d2cb3abfd8187d81b291290f9f.png', 1, 2200, 2200.00),
(150, 75, 'جاتو مناسبات', 'IMG_20260103_182811_857.jpg', 1, 25000, 25000.00),
(151, 76, 'جاتو', 'IMG_20260103_182817_552.jpg', 1, 22000, 22000.00),
(152, 76, 'معجنات بيتي فور', 'bakery-cookie-biscuit-baking-cake-cookies-pattern-dessert-biscuit-4b7c9b4e8ba4c744eb9312876025b568.png', 1, 1700, 1700.00),
(153, 77, 'عصير فراولة', 'milkshake-smoothie-strawberry-juice-chocolate-milk-juice-f6d6888a912cd2aea7f0e2f6643d4434.png', 1, 2500, 2500.00),
(154, 78, 'عصيرافوكادو ', 'IMG_20260103_182805_762.jpg', 1, 2500, 2500.00),
(155, 79, 'كب كيك', 'cupcake-icing-peanut-butter-cup-red-velvet-cake-cupcake-0566be047654cf293534cecbe5352ce0.png', 1, 500, 500.00),
(156, 80, 'جاتو مناسبات', 'IMG_20260103_182811_857.jpg', 1, 25000, 25000.00),
(157, 81, 'جاتو مذهب', '5bfb7554a930f-893b62f181b801bbf2c67dcf332dc37d.png', 1, 16000, 16000.00),
(158, 82, 'عصير منجا', 'healthy-useful-and-nutritious-drinks-thumbnail-186323.webp', 1, 2000, 2000.00),
(159, 83, 'جاتو مناسبات', 'IMG_20260103_182811_857.jpg', 1, 25000, 25000.00),
(160, 84, 'معجنات بيتي فور', 'bakery-cookie-biscuit-baking-cake-cookies-pattern-dessert-biscuit-4b7c9b4e8ba4c744eb9312876025b568.png', 1, 1700, 1700.00),
(161, 85, 'كب كيك', 'cupcake-icing-peanut-butter-cup-red-velvet-cake-cupcake-0566be047654cf293534cecbe5352ce0.png', 1, 500, 500.00),
(162, 86, 'جاتو مذهب', '5bfb7554a930f-893b62f181b801bbf2c67dcf332dc37d.png', 1, 16000, 16000.00);

-- --------------------------------------------------------

--
-- بنية الجدول `reservations`
--

CREATE TABLE `reservations` (
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `noOfGuests` int(50) NOT NULL,
  `reservedTime` time NOT NULL,
  `reservedDate` date NOT NULL,
  `reservedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','On Process','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `reservation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `reservations`
--

INSERT INTO `reservations` (`email`, `name`, `contact`, `noOfGuests`, `reservedTime`, `reservedDate`, `reservedAt`, `status`, `reservation_id`) VALUES
('asna@gmail.com', 'Asna Assalam', '0000000000', 6, '12:00:00', '2024-07-31', '2024-07-29 15:35:05', 'Completed', 1),
('zidnan@gmail.com', 'Zidnan', '1111111111', 5, '10:00:07', '2024-08-11', '2024-08-10 18:14:55', 'Pending', 2),
('preethi@gmail.com', 'Preethi Suresh', '5555555', 2, '06:30:59', '2024-08-10', '2024-08-03 18:15:54', 'On Process', 3),
('jhon@gmail.com', 'Jhon Paul', '334455', 9, '20:45:59', '2024-08-09', '2024-08-05 18:16:38', 'Cancelled', 4);

-- --------------------------------------------------------

--
-- بنية الجدول `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `review_date` date DEFAULT (CURRENT_DATE),
  `status` enum('approved','pending','rejected') DEFAULT 'pending',
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `reviews`
--

INSERT INTO `reviews` (`review_id`, `email`, `order_id`, `rating`, `review_text`, `review_date`, `status`, `response`) VALUES
(32, 'anas1@gmail.com', 61, 5, 'لذيذ جدااا تسلم الايادي', '2026-02-22', 'approved', NULL),
(33, 'asna@gmail.com', 59, 5, 'شكرا ', '2026-02-22', 'approved', NULL),
(34, 'anas1@gmail.com', 60, 5, 'ثانكس خيرات', '2026-02-23', 'approved', 'هذا من ذوقك'),
(35, 'anas1@gmail.com', 75, 5, 'اشكركم على التوصيل السريع والمعامله الجميله', '2026-02-25', 'approved', NULL),
(36, 'anas1@gmail.com', 77, 5, 'ثانكس', '2026-02-25', 'approved', NULL),
(37, 'anas1@gmail.com', 76, 5, 'ثانكس', '2026-02-25', 'approved', NULL),
(38, 'anas1@gmail.com', 79, 5, 'تسلم', '2026-02-25', 'approved', NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `role` enum('superadmin','admin','delivery boy','waiter') NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) NOT NULL DEFAULT 'default.jpg',
  `earnings` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `staff`
--

INSERT INTO `staff` (`id`, `firstName`, `lastName`, `email`, `contact`, `role`, `password`, `createdAt`, `updatedAt`, `profile_image`, `earnings`) VALUES
(2, 'دعاء ', 'الذبحاني', 'ak@gmail.com', '8877669955', 'superadmin', '123456', '2024-08-02 19:45:36', '2026-02-26 22:00:18', 'user-girl.png', 0.00),
(3, 'Ravi', 'Kumar', 'ravi@gmail.com', '9876543210', 'delivery boy', '$2y$10$mxAfoZmCKMik5OoiJBJzKOolg2H.erQlMbtANuUaCMUxhJLRWGpry', '2024-08-02 19:46:10', '2026-02-26 22:15:54', 'default.jpg', 28000.00),
(5, 'Hummy Cake', 'Admin', 'admin@gmail.com', '0000000000', 'admin', '123456', '2024-08-04 06:51:20', '2026-02-26 22:00:18', 'Gemini_Generated_Image_657mft657mft657m.png', 0.00),
(7, 'BADER', 'ALSHAMMARI', 'anas1@gmail.com', '0000000000', 'delivery boy', '123456', '2026-02-14 19:59:53', '2026-02-26 22:00:18', 'default.jpg', 1600.00);

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) NOT NULL DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`email`, `firstName`, `lastName`, `contact`, `password`, `dateCreated`, `profile_image`) VALUES
('anas1@gmail.com', 'ِAnas', 'zerooo', '0000000000', '123456', '2026-02-21 22:13:29', 'user-boy.jpg'),
('asna@gmail.com', 'Asna', 'Assalam', '3333333333', '123456', '2024-07-26 12:50:46', 'user-girl.png'),
('jhon@gmail.com', 'Jhon', 'Paul', '4444444444', '123456', '2024-08-10 15:37:56', 'default.jpg'),
('preethi@gmail.com', 'Preethi', 'Suresh', '2222222222', '123456', '2024-08-10 15:36:50', 'default.jpg'),
('zidnan@gmail.com', 'Zidnan', 'Ahamad', '1111111111', '123456', '2024-07-30 12:45:21', 'user-boy.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menucategory`
--
ALTER TABLE `menucategory`
  ADD PRIMARY KEY (`catId`);

--
-- Indexes for table `menuitem`
--
ALTER TABLE `menuitem`
  ADD PRIMARY KEY (`itemId`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `email` (`email`),
  ADD KEY `fk_delivery` (`delivery_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `itemId` (`itemName`) USING BTREE;

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `email` (`email`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `menucategory`
--
ALTER TABLE `menucategory`
  MODIFY `catId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `itemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_delivery` FOREIGN KEY (`delivery_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);

--
-- قيود الجداول `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- قيود الجداول `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
