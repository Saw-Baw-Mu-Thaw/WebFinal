-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1

-- Generation Time: May 15, 2025 at 02:41 AM

-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skeletondb`
--
CREATE DATABASE IF NOT EXISTS `skeletondb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skeletondb`;

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `LabelID` int(11) NOT NULL,
  `NoteID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Label` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`LabelID`, `NoteID`, `UserID`, `Label`) VALUES
(1, 3, 2, 'rotato');

-- --------------------------------------------------------

--
-- Table structure for table `lockednotes`
--

CREATE TABLE `lockednotes` (
  `NoteID` int(11) NOT NULL,
  `Password` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `NoteID` int(11) NOT NULL,
  `Title` varchar(50) DEFAULT NULL,
  `Location` varchar(150) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ModifiedDate` datetime NOT NULL,
  `AttachedImg` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`NoteID`, `Title`, `Location`, `UserID`, `ModifiedDate`, `AttachedImg`) VALUES
(1, 'demo', '../notes/bawbawbaw/demo.txt', 1, '2025-05-11 09:09:55', NULL),
(2, 'SharedNote', '../notes/bawbawbaw/SharedNote.txt', 1, '2025-05-11 09:41:01', NULL),
(3, 'My Nue Note', '../notes/Iroh/MyNueNote.txt', 2, '2025-05-11 12:17:30', NULL);

-- --------------------------------------------------------

--

-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `NoteID` int(11) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------


-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `OtpID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Code` int(11) NOT NULL,
  `Type` enum('activation','password_reset') NOT NULL,
  `ExpiresAt` datetime NOT NULL,
  `IsUsed` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------


-- Table structure for table `pinnednotes`
--

CREATE TABLE `pinnednotes` (
  `NoteID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Pinned` int(11) DEFAULT 0,
  `PinnedTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pinnednotes`
--

INSERT INTO `pinnednotes` (`NoteID`, `UserID`, `Pinned`, `PinnedTime`) VALUES
(1, 1, 0, NULL),
(2, 1, 1, '2025-05-11 09:16:17'),
(2, 2, 0, NULL),
(3, 2, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

CREATE TABLE `preferences` (
  `UserID` int(11) NOT NULL,
  `FontSize` int(11) DEFAULT 14,
  `Mode` varchar(5) DEFAULT 'LIGHT',
  `Layout` varchar(4) DEFAULT 'GRID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `preferences`
--

INSERT INTO `preferences` (`UserID`, `FontSize`, `Mode`, `Layout`) VALUES
(1, 14, 'LIGHT', 'LIST'),
(2, 14, 'DARK', 'GRID');

-- --------------------------------------------------------

--
-- Table structure for table `sharednotes`
--

CREATE TABLE `sharednotes` (
  `NoteID` int(11) NOT NULL,
  `OwnerID` int(11) NOT NULL,
  `Collaborator` int(11) NOT NULL,
  `Role` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sharednotes`
--

INSERT INTO `sharednotes` (`NoteID`, `OwnerID`, `Collaborator`, `Role`) VALUES
(2, 1, 2, 'VIEWER');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) DEFAULT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(60) NOT NULL,
  `Verified` int(11) DEFAULT 0,
  `ProfilePic` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Email`, `Password`, `Verified`, `ProfilePic`) VALUES
(1, 'bawbawbaw', 'bawbawbaw@gmail.com', '$2y$10$FAhpER8u.lR3UyELGxrB2u.jlgrMzaHUTi/8qizJilCjESjM6BpbC', 1, NULL),
(2, 'Iroh', 'Iroh@gmail.com', '$2y$10$gJMe4PWhOAkRlYHejs0hausuEPdL38HbRSDarq4z6NdC25U9Vo.vq', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`LabelID`),
  ADD KEY `NoteID` (`NoteID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `lockednotes`
--
ALTER TABLE `lockednotes`
  ADD KEY `NoteID` (`NoteID`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`NoteID`),
  ADD KEY `UserID` (`UserID`);

--

-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `NoteID` (`NoteID`);

--

-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`OtpID`),
  ADD KEY `UserID` (`UserID`);


-- Indexes for table `pinnednotes`
--
ALTER TABLE `pinnednotes`
  ADD PRIMARY KEY (`NoteID`,`UserID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `sharednotes`
--
ALTER TABLE `sharednotes`
  ADD PRIMARY KEY (`NoteID`,`OwnerID`,`Collaborator`),
  ADD KEY `OwnerID` (`OwnerID`),
  ADD KEY `Collaborator` (`Collaborator`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `LabelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `NoteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--

-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT;

--

-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `OtpID` int(11) NOT NULL AUTO_INCREMENT;


-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_ibfk_1` FOREIGN KEY (`NoteID`) REFERENCES `notes` (`NoteID`),
  ADD CONSTRAINT `labels_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `lockednotes`
--
ALTER TABLE `lockednotes`
  ADD CONSTRAINT `lockednotes_ibfk_1` FOREIGN KEY (`NoteID`) REFERENCES `notes` (`NoteID`);

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`NoteID`) REFERENCES `notes` (`NoteID`);

--

-- Constraints for table `otp`
--
ALTER TABLE `otp`
  ADD CONSTRAINT `otp_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

-- Constraints for table `pinnednotes`
--
ALTER TABLE `pinnednotes`
  ADD CONSTRAINT `pinnednotes_ibfk_1` FOREIGN KEY (`NoteID`) REFERENCES `notes` (`NoteID`),
  ADD CONSTRAINT `pinnednotes_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `sharednotes`
--
ALTER TABLE `sharednotes`
  ADD CONSTRAINT `sharednotes_ibfk_1` FOREIGN KEY (`NoteID`) REFERENCES `notes` (`NoteID`),
  ADD CONSTRAINT `sharednotes_ibfk_2` FOREIGN KEY (`OwnerID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `sharednotes_ibfk_3` FOREIGN KEY (`Collaborator`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
