-- Add NoteColor column to preferences table
ALTER TABLE `preferences` ADD COLUMN `NoteColor` varchar(7) DEFAULT '#ffffff' AFTER `FontSize`;

-- Update existing preferences to use default color
UPDATE `preferences` SET `NoteColor` = '#ffffff' WHERE `NoteColor` IS NULL; 