CREATE TABLE `otp` (
  `OtpID` INT AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NOT NULL,
  `Code` INT NOT NULL, -- Numeric OTP (e.g., 6-digit code)
  `Type` ENUM('activation', 'password_reset') NOT NULL,
  `ExpiresAt` DATETIME NOT NULL,
  `IsUsed` TINYINT(1) DEFAULT 0,
  `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`UserID`) REFERENCES `users`(`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

