-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 10, 2026 at 07:00 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_talipapa`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `store_profile_id` bigint UNSIGNED NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `stock` int UNSIGNED NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `store_profile_id`, `category_id`, `name`, `barcode`, `description`, `price`, `stock`, `image_path`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 'Cerelac Nestlé Infant Cereals Rice & Soya 120g', NULL, NULL, 289.00, 50, 'products/rmU03vf5IhVi0fV0Qo2s8JcCyIUwCRUCLjHifBPg.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:11:20'),
(2, 1, 21, 'Lucky Me Instant Noodles Chicken 55g', NULL, 'Experience chicken noodle soup Filipino-style with Lucky Me. Made with flat noodles in light chicken broth and topped with egg flakes, enjoy chicken mami (noodle soup) in an instant. Perfect for family dinners during cold days or a quick meal. Good for 1/3 of your daily requirement of vitamin A and iron. Get this 1 pack (55 grams) of Lucky Me Instant Noodles Chicken.', 85.00, 50, 'products/HEINBrqsoGl3xQW0Ti8Ji2BruSlh1OooM8W5YqZn.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 11:33:38'),
(3, 1, 21, 'Surf Detergent Powder Rose Fresh 65g', NULL, NULL, 60.00, 50, 'products/FWRecDPXF8lfdrF1unsztyf9aDXLo2SYPugVVfeb.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:11:40'),
(4, 1, 15, 'Silver Swan Sukang Puti 100ml', NULL, NULL, 54.00, 25, 'products/gs8OHf0Izrtl6iQ9r2E5HTXKFQGXj48Doxo89qR0.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:11:57'),
(5, 1, 15, 'Angel Evaporada Liquid Creamer 365ml', NULL, NULL, 59.00, 25, 'products/2n1SOqNCGkU9lEvrdvbUf8AkQtlqv7n98BfPmvbz.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:12:15'),
(6, 1, 15, 'Diğer Vaseline Gluta-Hya Serum Etkili Vücut Losyonu Güneş Koruyucu 150 Ml', NULL, NULL, 64.00, 25, 'products/1xYiAyUhrgay4Xr0cPaJlgHULOQtA2mt0lKzLp3K.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:12:34'),
(7, 1, 15, 'Off Family Care Insect Repellent Lotion 6ml', NULL, NULL, 69.00, 25, 'products/iakoM7Ub9dfFs8nx67YGuKs8yQYcTksbICmrFUHc.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:14:32'),
(8, 1, 15, 'Doreen Condensed Milk 390g', NULL, NULL, 74.00, 25, 'products/G4o87ezMmReXvEfDhr111ccCrCu33L2djQYfj8oI.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:15:15'),
(9, 1, 15, 'Super Crunch Corn Chips Sweet Corn 7g X 12\'s', NULL, NULL, 79.00, 25, 'products/RzxwrJCohwz5CeM36kv01uKYliaGVqsbfRVVwzK2.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:15:32'),
(10, 1, 15, 'Close Up Toothpaste Menthol Fresh 3x20g', NULL, NULL, 84.00, 25, 'products/4qlUfGaBkaqq9Vfg0YqueTTwC5SSrGZOZbJTC14A.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:16:05'),
(11, 1, 15, 'Alaska Evaporada Evaporated Creamer 370g', NULL, NULL, 89.00, 25, 'products/P25NLl2xvz7r0y0KdEcw0ts4Jb4miXvyGAcphUCs.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:16:18'),
(12, 1, 15, 'Deoplus Natural Tawas Powder 50g', NULL, NULL, 94.00, 25, 'products/ZR22bMRgsU4mfAtZlw4DdcQPQgpWl8wZQARDUjTg.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:16:43'),
(17, 1, 20, 'Doreen Condensed Milk 1L', NULL, NULL, 69.00, 25, 'products/j9KssnVFbb4FHu99nVrXKTaXn1D2NOSMGA19rddk.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:19:15'),
(19, 1, 20, 'Gatsby THC Moist Normal Treatment Hair Cream 100g', NULL, NULL, 79.00, 25, 'products/UnF3qleKav2lGi7WP9XRStCXZAH8En5MhSKZCp6Q.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:18:58'),
(20, 1, 20, 'Funkids Potitos Chips Cheese 60g', NULL, NULL, 84.00, 25, 'products/ECNQiPWBAFrK5S9Z5FuT8Ftscme09djv2a9oz48P.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:18:38'),
(21, 1, 20, '24 Seven Orange Powdered Mix Juice 40g', NULL, NULL, 89.00, 25, 'products/3oprIZblze3oWWzklhoZZ3WMmBzrD8H2ZfHSrcLN.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:17:48'),
(22, 1, 20, 'Nissin Bingo Sandwich Cookies Orange 10\'s', NULL, NULL, 94.00, 25, 'products/o4xYhX3tKz2c4zKfDmaihZuZ2vO4SWXuODCPtYf6.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:17:19'),
(23, 1, 20, 'Coco Mama Fresh Gata 200ml', NULL, NULL, 99.00, 25, 'products/sK1Adln5GmJCbAIP2dDQBiYW6vYZQG66kRhoZbp4.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:17:08'),
(24, 2, 5, 'Bear Brand Fortified Powdered Milk Drink', NULL, 'Full-cream fresh milk.', 98.00, 50, 'products/bGrmzGna8LMhbKxmsBOvpRq9hbh6VPXe0kCHjSyS.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 11:40:00'),
(25, 2, 20, 'Silver Swan Sukang Puti 20ml X 12\'s', NULL, NULL, 55.00, 50, 'products/bMbrDKmx5qBDzVts8BQiiaN9uKeB2Dzl6saXItcK.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:01:53'),
(26, 2, 5, 'Downy Fabcon Garden Bloom Fabric Conditioner 24ml 6s', NULL, 'Creamy plain yogurt.', 125.00, 50, 'products/8zE06Iir1KmfrQkw0vDd7ygZ5RtLV85NCbdEsDNj.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:01:22'),
(27, 2, 5, 'Lady\'s Choice Real Mayonnaise 220ml', NULL, 'Lady\'s Choice Real Mayonnaise makes sure you give your family the real love that makes everyday food special. Made with real eggs, healthy oils, a rich blend of spices, and other quality ingredients. It has a sour taste with a creamy texture and smells like eggs and mustard. Perfect for cold sauces, salad dressings, sandwich spreads, and fillings.', 54.00, 25, 'products/3xqxyb5YQsEDbp67fd5EfzXmDbUo7vd5LIwsMk41.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 11:58:25'),
(28, 2, 5, 'Lady\'s Choice Real Mayonnaise Regular 700ml', NULL, 'Lady\'s Choice Real Mayonnaise makes sure you give your family the real love that makes everyday food special. Made with real eggs, healthy oils, a rich blend of spices, and other quality ingredients. It has a sour taste with a creamy texture and smells like eggs and mustard. Perfect for cold sauces, salad dressings, sandwich spreads, and fillings. Get this 1 bottle containing 700ml of Lady\'s Choice Real Mayonnaise Regular.', 59.00, 25, 'products/xPBu8j2QQ7Bjg3zO5DIl8BnLSJsqORpPCy48m7sG.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 11:58:57'),
(29, 2, 5, 'Kopiko Double Cups Original 3-in-1 Coffee Mix 36g', NULL, 'Enjoy a cup of all-new and improved double cups. Perfect for the rainy season. Kopiko double cups are famous for their original coffee with a unique, distinctive taste. The classic taste enjoyed by all. It can be served hot or cold. Get this 1 twin pack (36 grams) of Kopiko Double Cups.', 64.00, 25, 'products/B3L07VxcK4I3xdTwiffTjadJ9VTaz070GCHSrlBM.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:00:23'),
(30, 2, 5, 'Alaska Condensada Sweetened Condensed Creamer Value Pack 560g', NULL, NULL, 69.00, 25, 'products/kDTztXPS0ioQofHhzez0A6EcS2iYoJkTssTwdObc.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:02:22'),
(31, 2, 5, 'Young\'s Town Premium Corned Beef 150g', NULL, NULL, 74.00, 25, 'products/cqXG7xsQpis2HswMbcL3ruFP3CtZS8jtaj09UPIC.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:03:17'),
(32, 2, 5, 'Bioderm Family Germicidal Soap Coolness 60g', NULL, NULL, 79.00, 25, 'products/1iul3Pqp5i77rvw0i8n8tw1MjrkrVsww5n81CgEF.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:03:35'),
(33, 2, 5, 'Fudgee Bar Cake Vanilla Flavor 39g', NULL, NULL, 84.00, 25, 'products/lXemRZ5C9qaG5z3CSTwLJtI5AUeYnRg9dx1yAPSx.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:04:00'),
(34, 2, 5, 'Queen Pure Baking Soda 125g', NULL, NULL, 89.00, 25, 'products/1Owc7kVPOs3UcRSjvdsgOpb3uqgqfPTztHdOW1yI.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:04:14'),
(35, 2, 5, 'Jack \'n Jill Choco Knots', NULL, NULL, 94.00, 25, 'products/oegByos6DLPHYv6nqgHWgb0WzPAXGqkkzcgQfHg7.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:04:37'),
(36, 2, 5, 'Jack \'n Jill Piattos Potato Crisps Cheese 40g', NULL, NULL, 99.00, 25, 'products/QgTNJEIsjD5aTuFvoCA5Mcd3xDVVrzgae9m5LMo6.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:04:46'),
(37, 2, 20, 'Mega Prime Fruit Cocktail EOC 850g', NULL, NULL, 54.00, 25, 'products/n3YS6RemfgsLt3gdjUWdlX8C0CXynhEmuW7UeVeQ.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:05:30'),
(38, 2, 20, 'Cream All Purpose Flour 400g', NULL, NULL, 59.00, 25, 'products/3KuDlPjGTfAB5QNI1xWYW8b6XRWq9tCON3ZaWRZK.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:05:47'),
(39, 2, 20, 'Gatorade Sports Drink Blue Bolt 350mL', NULL, NULL, 64.00, 25, 'products/82YRE1cM2FbWHLW0LQZLunfdUH1QY8M71lGxl0sO.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:06:04'),
(40, 2, 20, 'Cream Pure Cornstarch', NULL, NULL, 69.00, 25, 'products/N7QyjGD7vTqYC4eJ8ZIErbw3Iv80Qtx8r6mOQJsV.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:06:23'),
(41, 2, 20, 'Magic Creams Butter 28g 11s', NULL, NULL, 74.00, 25, 'products/jjRRKCVVsckMOiUkSRDHRkwPRwrzPwrCgQRv0PXb.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:06:48'),
(42, 2, 20, 'Maya Hotcake Mix Original - 500g', NULL, NULL, 79.00, 25, 'products/GqPGflk6Mo30r1XP2cQMYcYKIJuCBSwHN7iHJBIX.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:08:48'),
(43, 2, 20, 'Starwax Red Dye Floor Wax 90g', NULL, NULL, 84.00, 25, 'products/jwCbUdxgn5IzwTwYCkY1Mtc8j2RijOLfs5voBzhu.png', 1, '2026-05-29 10:24:56', '2026-05-29 12:08:23'),
(44, 2, 20, 'IPI Aceite De Manzanilla 25ml', NULL, NULL, 89.00, 25, 'products/jOzTdhIkQGDZQiivboNPOGUcourDKfaKLzpPYazH.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:07:57'),
(45, 2, 20, 'Maya Chocolate Hotcake Mix Chocolatey 200g', NULL, NULL, 94.00, 25, 'products/O6WtvbQstj5e0y1eagOReozZjMyJmGieuFl0a7l9.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:07:29'),
(46, 2, 20, 'Surf Detergent Powder Cherry Blossom 65g 6\'s + Get 1 Free', NULL, NULL, 99.00, 25, 'products/ZUqXvZnPp7CBdshLZSt9JLrTB4WEfFVttRZM3o0a.jpg', 1, '2026-05-29 10:24:56', '2026-05-29 12:07:10'),
(47, 3, 6, 'Summit Natural Drinking Water 1L', NULL, 'The official bottled water of the Philippine National Athletes. It has minerals to boost their hydration level. This product is purified and processed through state-of-the-art equipment. It comes in an environment-friendly bottle to help save the environment. Helps dissolve minerals and nutrients, making them more accessible to the body. Get this 1 bottle contains 1000ml of Summit Mineral Water.', 95.00, 50, 'products/5stD6AvGNqDG72QhfJDds7rrP8RTzLhWQiJJTauR.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 11:52:30'),
(50, 3, 6, 'Coca-Cola Coke Mismo 290ml', NULL, 'Energize your senses with Coke. Best enjoyed cold for maximum refreshment. Perfect drink in the afternoon, on the go, with your meal, or any time. Good for parties, meals, and celebrations big and small. Get this 1 bottle (290ml) of Coke Mismo.', 25.00, 23, 'products/eBajAHfAywY1R7rMLKtMj65XJeUniceHUU86jf9T.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:35:34'),
(51, 3, 6, 'Ds Asien Supermarkt Gmbh Tanduay Rum 75cl', NULL, 'A unique blend of cane spirits derived from choice sugarcane blended to a suave 60 proof. It holds the true character of true Asian rum buoyed by more than a century and a half of creative innovation and foresight. This rhum reflects the hallmark of Tanduay’s rich and lively heritage. Get this 1 bottle (750ml) of Tanduay Rhum 5 Years.', 59.00, 25, 'products/GmYFiP8m5ShdfiWmyfbq7W26imGVxjTkmoMlbf7P.png', 1, '2026-05-29 10:24:57', '2026-05-29 11:50:21'),
(52, 3, 6, 'Sting Energy Drink Strawberry Flavor 12x300ml', NULL, 'Sting Energy Drink Strawberry Flavor 12x300ml', 64.00, 25, 'products/Qg37u3eNKCK4evGGXTilnXPMW62rCFongjEPMIcN.png', 1, '2026-05-29 10:24:57', '2026-05-29 11:51:40'),
(53, 3, 6, 'Nature\'s Spring Distilled Water 6.6L', NULL, 'Nature\'s Spring Distilled Drinking Water is the mothers\' go-to water brand. Recommended by Pediatricians due to its quality, purity, safety, and affordability. Helps dissolve minerals and nutrients, making them more accessible to the body. It undergoes a 12-stage process with different levels of filtration, purification, and distillation processes to remove impurities using state-of-the-art facilities.', 69.00, 25, 'products/GwkF5PyGl1XSHwNHdHJRSqNVwVoswskHpPOChQCk.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 11:53:54'),
(55, 3, 6, 'Great Taste Premium Blend 25gx48', NULL, NULL, 79.00, 25, 'products/02B7QRSVrhoMYPZYzNNHWnZga5DN4Izvq9oxHB0X.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:20:56'),
(56, 3, 6, 'Stickman Wafer Stick Chocolate Jumbo 130\'s', NULL, NULL, 84.00, 25, 'products/jQCSAM1rP8FbuDClEEzRn6dZhdlb8cNx7zuM61f9.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:21:21'),
(57, 3, 6, 'Keratin Plus Luxurious Brazilian Hair Treatment Red 20g', NULL, NULL, 89.00, 25, 'products/21qdlABScoOArNK1i0Q8XztajHu6YkAqZNJCyxv1.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:21:42'),
(59, 3, 6, 'Chocolita Πραλίνα Φουντουκιού 1Kg', NULL, NULL, 99.00, 25, 'products/7PXfbwOexX6loZAkYX5MmPV2Y5zqBywjmMX3v3BA.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:21:56'),
(61, 3, 8, 'Mama Sita\'s Oyster Sauce 30g', NULL, NULL, 59.00, 25, 'products/jk72F1FjjzEAI9p7zDI87eWoS3xcX6XqxF704Fv0.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:22:13'),
(62, 3, 8, 'Tang Powdered Juice Fruit And Veg Dalandan + Malunggay 19g', NULL, NULL, 64.00, 25, 'products/wsJ0LYoNyQydlK1ycmU21zq5s5RadvJbLm17lSme.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:22:27'),
(63, 3, 8, 'Nature\'s Spring Purified Drinking Water 1.5L', NULL, NULL, 69.00, 25, 'products/5RiZh0eMYOJ4vqGKB2Zbg9GJfcw81Fac3vWZRjXl.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:22:45'),
(64, 3, 8, 'Birch Tree Fortified Powdered Chocolate Milk Drink 290g', NULL, NULL, 74.00, 25, 'products/8ePMQsKaws3NjZ51gFcV8JeGEtnrfOu8drp6VpR3.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:23:06'),
(65, 3, 8, 'Uni-pak Sardines In Tomato Sauce 155g', NULL, NULL, 79.00, 25, 'products/TKRblTZypLfaCMf289jBEN1YXYqhqz8MBlNI9lZR.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:23:24'),
(66, 3, 8, 'Rebisco Extreme Sandwich Ultimate Choco 10\'s', NULL, NULL, 84.00, 25, 'products/tQHyMFqYzDtxolfNxhRNY7bJ8rAOk1G6mFNnuUBN.png', 1, '2026-05-29 10:24:57', '2026-05-29 12:24:22'),
(67, 3, 8, 'Ajinomoto Crispy Fry Breading Mix Original 30g', NULL, NULL, 89.00, 25, 'products/TIYvkfi1f2FvKntMbMkpLH2ppSiqENdBqeUxxrPG.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:24:52'),
(68, 3, 8, 'Datu Puti Spiced Vinegar', NULL, NULL, 94.00, 25, 'products/RwBe6jp55wC8E0Vz6cE1WecUbirCREbWChwhmCyA.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:25:10'),
(69, 3, 8, 'Selecta Bestsellers 2-in-1 Cookies & Cream + Double Dutch 750mL', NULL, NULL, 99.00, 25, 'products/BJZWTbUElkOsHWyGu8R3gszljU6x3qhX12ae7b0C.jpg', 1, '2026-05-29 10:24:57', '2026-05-29 12:25:30'),
(70, 1, 15, 'Jasmine Rice 5kg', NULL, 'Premium long-grain jasmine rice.', 289.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(71, 1, 20, 'Fresh Tomatoes 1kg', NULL, 'Locally sourced ripe tomatoes.', 85.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(72, 1, 21, 'Pandesal (12 pcs)', NULL, 'Freshly baked Filipino bread rolls.', 60.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(73, 1, 19, 'Tuyo (Dried Fish) 100g', NULL, 'Budget dried herring. Barato na tuyo for everyday meals.', 35.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(74, 1, 19, 'Premium Tuyo 250g', NULL, 'Large dried fish, premium pack.', 95.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(75, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 1', NULL, 'Demo highlight product for category carousel.', 54.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(76, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 2', NULL, 'Demo highlight product for category carousel.', 59.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(77, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 3', NULL, 'Demo highlight product for category carousel.', 64.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(78, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 4', NULL, 'Demo highlight product for category carousel.', 69.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(79, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 5', NULL, 'Demo highlight product for category carousel.', 74.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(80, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 6', NULL, 'Demo highlight product for category carousel.', 79.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(81, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 7', NULL, 'Demo highlight product for category carousel.', 84.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(82, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 8', NULL, 'Demo highlight product for category carousel.', 89.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(83, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 9', NULL, 'Demo highlight product for category carousel.', 94.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(84, 1, 15, 'Talipapa Fresh Mart — Grains pasta sides Item 10', NULL, 'Demo highlight product for category carousel.', 99.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(85, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 1', NULL, 'Demo highlight product for category carousel.', 54.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(86, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 2', NULL, 'Demo highlight product for category carousel.', 59.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(87, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 3', NULL, 'Demo highlight product for category carousel.', 64.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(88, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 4', NULL, 'Demo highlight product for category carousel.', 69.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(89, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 5', NULL, 'Demo highlight product for category carousel.', 74.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(90, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 6', NULL, 'Demo highlight product for category carousel.', 79.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(91, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 7', NULL, 'Demo highlight product for category carousel.', 84.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(92, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 8', NULL, 'Demo highlight product for category carousel.', 89.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(93, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 9', NULL, 'Demo highlight product for category carousel.', 94.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(94, 1, 20, 'Talipapa Fresh Mart — Fruits vegetables Item 10', NULL, 'Demo highlight product for category carousel.', 99.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(95, 2, 5, 'Fresh Milk 1L', NULL, 'Full-cream fresh cow milk.', 98.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(96, 2, 5, 'Nestle Vegan Oat Milk 300ml', NULL, 'Plant-based vegan oat milk. Dairy-free, not from cow.', 89.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(97, 2, 20, 'Banana Bundle 1kg', NULL, 'Ripe Saba bananas.', 55.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(98, 2, 5, 'Greek Yogurt 450g', NULL, 'Creamy plain yogurt.', 125.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(99, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 1', NULL, 'Demo highlight product for category carousel.', 54.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(100, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 2', NULL, 'Demo highlight product for category carousel.', 59.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(101, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 3', NULL, 'Demo highlight product for category carousel.', 64.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(102, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 4', NULL, 'Demo highlight product for category carousel.', 69.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(103, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 5', NULL, 'Demo highlight product for category carousel.', 74.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(104, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 6', NULL, 'Demo highlight product for category carousel.', 79.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(105, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 7', NULL, 'Demo highlight product for category carousel.', 84.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(106, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 8', NULL, 'Demo highlight product for category carousel.', 89.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(107, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 9', NULL, 'Demo highlight product for category carousel.', 94.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(108, 2, 5, 'Green Basket Groceries — Dairy eggs cheese Item 10', NULL, 'Demo highlight product for category carousel.', 99.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(109, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 1', NULL, 'Demo highlight product for category carousel.', 54.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(110, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 2', NULL, 'Demo highlight product for category carousel.', 59.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(111, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 3', NULL, 'Demo highlight product for category carousel.', 64.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(112, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 4', NULL, 'Demo highlight product for category carousel.', 69.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(113, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 5', NULL, 'Demo highlight product for category carousel.', 74.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(114, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 6', NULL, 'Demo highlight product for category carousel.', 79.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(115, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 7', NULL, 'Demo highlight product for category carousel.', 84.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(116, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 8', NULL, 'Demo highlight product for category carousel.', 89.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(117, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 9', NULL, 'Demo highlight product for category carousel.', 94.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(118, 2, 20, 'Green Basket Groceries — Fruits vegetables Item 10', NULL, 'Demo highlight product for category carousel.', 99.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:26', '2026-09-09 02:25:26'),
(119, 3, 6, 'Bottled Water 1L (6-pack)', NULL, 'Purified drinking water.', 95.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(120, 3, 8, 'Potato Chips 150g', NULL, 'Classic salted potato chips.', 65.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(121, 3, 15, 'Instant Noodles (5-pack)', NULL, 'Assorted flavors.', 75.00, 50, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(122, 3, 6, 'QuickStop Pantry — Beverages Item 1', NULL, 'Demo highlight product for category carousel.', 54.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(123, 3, 6, 'QuickStop Pantry — Beverages Item 2', NULL, 'Demo highlight product for category carousel.', 59.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(124, 3, 6, 'QuickStop Pantry — Beverages Item 3', NULL, 'Demo highlight product for category carousel.', 64.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(125, 3, 6, 'QuickStop Pantry — Beverages Item 4', NULL, 'Demo highlight product for category carousel.', 69.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(126, 3, 6, 'QuickStop Pantry — Beverages Item 5', NULL, 'Demo highlight product for category carousel.', 74.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(127, 3, 6, 'QuickStop Pantry — Beverages Item 6', NULL, 'Demo highlight product for category carousel.', 79.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(128, 3, 6, 'QuickStop Pantry — Beverages Item 7', NULL, 'Demo highlight product for category carousel.', 84.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(129, 3, 6, 'QuickStop Pantry — Beverages Item 8', NULL, 'Demo highlight product for category carousel.', 89.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(130, 3, 6, 'QuickStop Pantry — Beverages Item 9', NULL, 'Demo highlight product for category carousel.', 94.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(131, 3, 6, 'QuickStop Pantry — Beverages Item 10', NULL, 'Demo highlight product for category carousel.', 99.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(132, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 1', NULL, 'Demo highlight product for category carousel.', 54.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(133, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 2', NULL, 'Demo highlight product for category carousel.', 59.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(134, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 3', NULL, 'Demo highlight product for category carousel.', 64.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(135, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 4', NULL, 'Demo highlight product for category carousel.', 69.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(136, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 5', NULL, 'Demo highlight product for category carousel.', 74.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(137, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 6', NULL, 'Demo highlight product for category carousel.', 79.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(138, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 7', NULL, 'Demo highlight product for category carousel.', 84.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(139, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 8', NULL, 'Demo highlight product for category carousel.', 89.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(140, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 9', NULL, 'Demo highlight product for category carousel.', 94.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27'),
(141, 3, 8, 'QuickStop Pantry — Cookies snacks candy Item 10', NULL, 'Demo highlight product for category carousel.', 99.00, 25, 'img/placeholders/product.svg', 1, '2026-09-09 02:25:27', '2026-09-09 02:25:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_store_profile_id_barcode_unique` (`store_profile_id`,`barcode`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_store_profile_id_foreign` FOREIGN KEY (`store_profile_id`) REFERENCES `store_profiles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
