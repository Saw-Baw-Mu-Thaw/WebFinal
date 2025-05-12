-- Table for storing notifications related to shared notes
CREATE TABLE `notifications` (
  `NotificationID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `NoteID` int(11) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` datetime NOT NULL,
  PRIMARY KEY (`NotificationID`),
  KEY `UserID` (`UserID`),
  KEY `NoteID` (`NoteID`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`NoteID`) REFERENCES `notes` (`NoteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 