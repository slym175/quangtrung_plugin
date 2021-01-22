-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 18, 2021 lúc 04:42 AM
-- Phiên bản máy phục vụ: 10.4.17-MariaDB
-- Phiên bản PHP: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `wordpress_quangtrung`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wp_hoidap`
--

CREATE TABLE `wp_hoidap` (
  `id` mediumint(9) NOT NULL,
  `name` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` tinytext COLLATE utf8mb4_unicode_ci NOT NULL,
  `contents` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `wp_hoidap`
--

INSERT INTO `wp_hoidap` (`id`, `name`, `phone`, `email`, `question_type`, `contents`, `link`, `created`) VALUES
(1, 'Thủy', '0986114671', 'thuyhu9876@gmail.com', 'Câu hỏi kỹ thuật', 'Làm sao để pro ạ??', 'http://localhost/wordpress_quangtrung/product/may-cua-thit-bang-doc-avantco-embs65ss-65-luoi-thep-khong-gi-1-hp-120v-2/', '2021-01-15 05:40:44'),
(2, 'Thủy Lương', '0969008515', 'thuyluong2500s@gmail.com', 'Câu hỏi thông thường', 'Có cách nào không làm mà vẫn có ăn không ạ', 'http://localhost/wordpress_quangtrung/product/may-cua-thit-bang-doc-avantco-embs65ss-65-luoi-thep-khong-gi-1-hp-120v-2/', '2021-01-15 05:40:47'),
(9, 'Đỗ Hữu Nghĩa', '0969008515', 'nghiado293@gmail.com', 'Câu hỏi thông thường', 'AFA', 'http://localhost/wordpress_quangtrung/product/leu-cam-trai-8-10-nguoi-naturehike-nh17t800-t/', '2021-01-15 07:46:39'),
(10, 'Đỗ Hữu Nghĩa', '0969008515', 'nghiado293@gmail.com', 'Câu hỏi kỹ thuật', 'DD', 'http://localhost/wordpress_quangtrung/product/leu-cam-trai-8-10-nguoi-naturehike-nh17t800-t/', '2021-01-15 07:52:34');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `wp_hoidap`
--
ALTER TABLE `wp_hoidap`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `wp_hoidap`
--
ALTER TABLE `wp_hoidap`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
