-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-12-27 15:13:54
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `schema`
--

-- --------------------------------------------------------

--
-- 資料表結構 `movie`
--

CREATE TABLE `movie` (
  `MovieID` int(15) NOT NULL COMMENT '電影編號',
  `Title` varchar(255) NOT NULL COMMENT '電影名稱',
  `Genre` varchar(255) NOT NULL COMMENT '電影類型',
  `Duration` int(11) NOT NULL COMMENT '電影時長(分鐘)',
  `img` varchar(100) NOT NULL COMMENT '電影圖片的路徑'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `movie`
--

INSERT INTO `movie` (`MovieID`, `Title`, `Genre`, `Duration`, `img`) VALUES
(1, '希希的下北澤之旅', '愛情', 135, 'resource/xixi.jpg'),
(10, '成都超人', '動畫', 120, 'resource/super.jpeg');

-- --------------------------------------------------------

--
-- 資料表結構 `screening`
--

CREATE TABLE `screening` (
  `ScreeningID` int(15) NOT NULL COMMENT '場次編號',
  `MovieID` int(15) NOT NULL COMMENT '電影編號',
  `StartTime` datetime NOT NULL COMMENT '場次開始時間',
  `Hall` varchar(20) NOT NULL COMMENT '場次廳別',
  `Price` int(11) NOT NULL COMMENT '場次票價',
  `TotalSeats` int(11) NOT NULL COMMENT '場次總座位',
  `AvailableSeats` int(11) NOT NULL COMMENT '場次剩餘座位'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `screening`
--

INSERT INTO `screening` (`ScreeningID`, `MovieID`, `StartTime`, `Hall`, `Price`, `TotalSeats`, `AvailableSeats`) VALUES
(1, 1, '2025-11-04 05:14:00', 'B廳', 350, 100, 100),
(17, 10, '2025-11-11 09:00:00', 'A廳', 300, 100, 100);

-- --------------------------------------------------------

--
-- 資料表結構 `ticket`
--

CREATE TABLE `ticket` (
  `TicketID` int(15) NOT NULL COMMENT '訂單編號',
  `ScreeningID` int(15) NOT NULL COMMENT '場次編號',
  `UserName` varchar(255) NOT NULL COMMENT '訂單顧客姓名',
  `SeatNumber` varchar(10) NOT NULL COMMENT '坐位號碼',
  `PurchaseTime` datetime NOT NULL COMMENT '訂購時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `UserName` varchar(255) NOT NULL COMMENT '使用者名稱',
  `password_hash` varchar(255) NOT NULL COMMENT '密碼hash',
  `IsAdmin` tinyint(1) NOT NULL COMMENT '是否為admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`MovieID`);

--
-- 資料表索引 `screening`
--
ALTER TABLE `screening`
  ADD PRIMARY KEY (`ScreeningID`),
  ADD KEY `MovieID` (`MovieID`);

--
-- 資料表索引 `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`TicketID`),
  ADD KEY `ScreeningID` (`ScreeningID`),
  ADD KEY `UserName` (`UserName`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserName`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `ticket`
--
ALTER TABLE `ticket`
  MODIFY `TicketID` int(15) NOT NULL AUTO_INCREMENT COMMENT '訂單編號';

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `screening`
--
ALTER TABLE `screening`
  ADD CONSTRAINT `screening_ibfk_1` FOREIGN KEY (`MovieID`) REFERENCES `movie` (`MovieID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`ScreeningID`) REFERENCES `screening` (`ScreeningID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`UserName`) REFERENCES `users` (`UserName`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
