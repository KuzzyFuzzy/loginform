-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2024 at 03:15 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_databasekay`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'tabayankyla@gmail.com', '8ba1d30ea792c2d25d92b4679d8acdf2781979981d6a06dddfe3535fbba3b6a9be2d3ece834925402042df6c3179b2eae535', '2024-09-05 17:42:19', '2024-09-05 14:42:19'),
(2, 'tabayankyla@gmail.com', 'cd76ebc7643a97442dc203abf72eddb8249e2137bcdee205f151da8be37ab87351523cf92f4730d10d913766861f831dff85', '2024-09-05 17:42:45', '2024-09-05 14:42:45'),
(3, 'tabayankyla@gmail.com', 'd87bdbc0792631052e010b644e47af8947ea2874c5d4cd5787254dce1d8b509d60568ddb5f1d5d9483123407759753369823', '2024-09-05 17:42:55', '2024-09-05 14:42:55'),
(4, 'tabayankyla@gmail.com', '1ac969281aab3ac6bc922f3cf22a3f1eb90d995008207cf73247bdadef0632dd13988c27a19050271157393f92e86f79ea69', '2024-09-05 17:43:08', '2024-09-05 14:43:08'),
(5, 'tabayankyla@gmail.com', 'ad3b319a320f7bfeba7ef717f9b8a73e9fb29cb18c15e4fc903f639e92254a9c48b1a087beddbc1b2d0825d7402f5c09044a', '2024-09-05 17:43:30', '2024-09-05 14:43:30'),
(6, 'tabayankyla@gmail.com', '403068495d935e08614f4070e88821693bf2dac9d2faa8b98fa66ad0c1ef743b6ffc020c6a995e1149dfc93009120630f91d', '2024-09-05 17:49:03', '2024-09-05 14:49:03'),
(7, 'tabayankyla@gmail.com', '836fc44ad7e40e8e2b546600d35f5e465e0cf0f49b1e077bbd2e5410a8d44b086021a9eba176fea69ed4069815800a84aed7', '2024-09-05 17:52:47', '2024-09-05 14:52:47'),
(8, 'tabayankyla@gmail.com', 'd4ffb71863478cc9ad6524e07c702aca566e5adb21403f52e3df98065c0d6b8d29b6fd8011e5b285938226a17cf021ffd39d', '2024-09-05 17:56:41', '2024-09-05 14:56:41');

-- --------------------------------------------------------

--
-- Table structure for table `profile_images`
--

CREATE TABLE `profile_images` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `photo` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `attendance` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `middle_name`, `last_name`, `email`, `status`, `photo`, `birthday`, `attendance`) VALUES
(1, 'Kyla Jane', 'Amores', 'Tabayan', 'kyla@gmail.com', 1, 'uploads/student_photos/kyla.jpg', '2002-12-17', 'Present'),
(2, 'keith', 'keiths', 'ellaga', 'k@gmail.com', 0, 'uploads/student_photos/2b8c8a71-d746-4480-a8f2-cd28ebe1547f.jfif', '2003-01-21', 'Present'),
(4, 'dasd', 'dasd', 'dsad', 'kja@gmail.com', 1, 'uploads/student_photos/134358703_1096701494112537_4033345827389153277_n.jpg', '2024-10-02', 'Present'),
(12, 'asdadsa', 'sdada', 'dsada', 'sas@gmail.com', 1, 'uploads/student_photos/245819955_1284012158714802_5467948783661012151_n.jpg', '2024-09-29', 'Present'),
(231, 'sdadasd', 'sdasac', 'asawqds', 'asaaa@gmail.com', 0, 'uploads/student_photos/279364240_1414305332352150_3717821986541005283_n.jpg', '2024-09-29', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthday` date NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_attempts` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `session_id` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'uploads/profile_images/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `middle_name`, `last_name`, `age`, `gender`, `birthday`, `contact_number`, `address`, `photo`, `email`, `password`, `login_attempts`, `status`, `session_id`, `profile_image`) VALUES
(35, 'Kyla Jane', 'Amores', 'Tabayan', 10, 'Female', '2024-09-22', '09354472200', 'tboli sot cot', 'uploads/kyla.jpg', 'kj@gmail.com', '12345', 0, 1, 'm78u5cidovb7igoqnorv5gqgkr', 'uploads/profile_images/default.png'),
(41, 'daad', 'N/A', 'dasda', 2, 'Male', '2024-09-29', '32132132554', 'dsa', 'uploads/157929687_1137853156664037_1041483430679586112_n.jpg', 'ken@gmail.com', 'ken123', 0, 1, 'h0dt3nkbljsdvj44b8h40j9ggn', 'uploads/profile_images/default.png'),
(42, 'aasd', 'N/A', 'jhgfcas', 23, 'Male', '2024-09-29', '43242341341', 'sdqsadad', 'uploads/277767027_535101564704145_3077233894945692342_n.jpg', 'ken2@gmail.com', 'ken123', 0, 0, '', 'uploads/profile_images/default.png'),
(43, 'dassvc', 'N/A', 'cwcdcs', 232, 'Male', '2024-09-29', '23131312313', 'sadawads', 'uploads/287500889_1127255541192736_940457382519938586_n.jpg', 'ken3@gmail.com', 'ken123', 0, 0, '', 'uploads/profile_images/default.png');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `title`, `file_path`) VALUES
(37, 'SpongeBob 1', 'uploads/videos/6.mp4'),
(45, 'SpongeBob 2', 'uploads/videos/SPONGEBOB 123.mp4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profile_images`
--
ALTER TABLE `profile_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `profile_images`
--
ALTER TABLE `profile_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `profile_images`
--
ALTER TABLE `profile_images`
  ADD CONSTRAINT `profile_images_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
