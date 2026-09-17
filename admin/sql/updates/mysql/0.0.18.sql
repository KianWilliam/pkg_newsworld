ALTER TABLE `#__newsworld` ADD `latitude` DECIMAL(9,7) NOT NULL DEFAULT 54.65;
ALTER TABLE `#__newsworld` ADD `longitude` DECIMAL(10,7) NOT NULL DEFAULT -5.67;
UPDATE `#__newsworld` SET `latitude` = 54.65, `longitude` = -5.67;