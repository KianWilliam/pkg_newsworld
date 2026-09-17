ALTER TABLE`#__newsworld` ADD COLUMN `ordering` int(11) NOT NULL DEFAULT '0' AFTER `language`;
UPDATE `#__newsworld` SET `ordering` = `id`;