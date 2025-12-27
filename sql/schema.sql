-- --------------------------------------------------------
-- 建立資料庫
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `schema` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `schema`;

-- --------------------------------------------------------
-- 刪除舊表，避免重複
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ticket`;
DROP TABLE IF EXISTS `screening`;
DROP TABLE IF EXISTS `movie`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- 建立 users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `UserName` varchar(255) NOT NULL COMMENT '使用者名稱',
  `password_hash` varchar(255) NOT NULL COMMENT '密碼hash',
  `IsAdmin` boolean NOT NULL COMMENT '是否為admin',
  PRIMARY KEY (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- 建立 movie
-- --------------------------------------------------------
CREATE TABLE `movie` (
  `MovieID` int(15) NOT NULL COMMENT '電影編號',
  `Title` varchar(255) NOT NULL COMMENT '電影名稱',
  `Genre` varchar(255) NOT NULL COMMENT '電影類型',
  `Duration` int(11) NOT NULL COMMENT '電影時長(分鐘)',
  PRIMARY KEY (`MovieID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 範例資料
INSERT INTO `movie` (`MovieID`, `Title`, `Genre`, `Duration`) VALUES
(0, '希希的下北澤之旅', '愛情', 135),
(10, '成都超人', '動畫', 120);

-- --------------------------------------------------------
-- 建立 screening
-- --------------------------------------------------------
CREATE TABLE `screening` (
  `ScreeningID` int(15) NOT NULL COMMENT '場次編號',
  `MovieID` int(15) NOT NULL COMMENT '電影編號',
  `StartTime` datetime NOT NULL COMMENT '場次開始時間',
  `Hall` varchar(20) NOT NULL COMMENT '場次廳別',
  `Price` int(11) NOT NULL COMMENT '場次票價',
  `TotalSeats` int(11) NOT NULL COMMENT '場次總座位',
  `AvailableSeats` int(11) NOT NULL COMMENT '場次剩餘座位',
  PRIMARY KEY (`ScreeningID`),
  KEY `MovieID` (`MovieID`),
  CONSTRAINT `screening_ibfk_1` FOREIGN KEY (`MovieID`) REFERENCES `movie` (`MovieID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 範例資料
INSERT INTO `screening` (`ScreeningID`, `MovieID`, `StartTime`, `Hall`, `Price`, `TotalSeats`, `AvailableSeats`) VALUES
(1, 0, '2025-11-04 05:14:00', 'B廳', 350, 100, 100),
(17, 10, '2025-11-11 09:00:00', 'A廳', 300, 100, 100);

-- --------------------------------------------------------
-- 建立 ticket
-- --------------------------------------------------------
CREATE TABLE `ticket` (
  `TicketID` int(15) NOT NULL AUTO_INCREMENT COMMENT '訂單編號',
  `ScreeningID` int(15) NOT NULL COMMENT '場次編號',
  `UserName` varchar(255) NOT NULL COMMENT '訂單顧客姓名',
  `SeatNumber` varchar(10) NOT NULL COMMENT '坐位號碼',
  `PurchaseTime` datetime NOT NULL COMMENT '訂購時間',
  PRIMARY KEY (`TicketID`),
  KEY `ScreeningID` (`ScreeningID`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`ScreeningID`) REFERENCES `screening` (`ScreeningID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`UserName`) REFERENCES `users` (`UserName`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;