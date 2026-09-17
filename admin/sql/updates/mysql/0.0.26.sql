ALTER TABLE `#__newsworld` DROP COLUMN `ordering`;
ALTER TABLE `#__newsworld` ADD COLUMN `parent_id` INT(10) NOT NULL DEFAULT '1' AFTER `language`;
ALTER TABLE `#__newsworld` ADD COLUMN `level`	int(10)    NOT NULL DEFAULT '0' AFTER `parent_id`;
ALTER TABLE `#__newsworld` ADD COLUMN `path`	varchar(400)    NOT NULL DEFAULT '' AFTER `level`;
ALTER TABLE `#__newsworld` ADD COLUMN `lft`	int(11)    NOT NULL DEFAULT '0' AFTER `path`;
ALTER TABLE `#__newsworld` ADD COLUMN `rgt`	int(11)    NOT NULL DEFAULT '0' AFTER `lft`;
UPDATE `#__newsworld` SET `path` = `alias`;