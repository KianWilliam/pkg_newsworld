ALTER TABLE `#__newsworld` ADD COLUMN `language` CHAR(7) NOT NULL DEFAULT '*' AFTER `alias`;

DROP INDEX `aliasindex` on `#__newsworld`;
CREATE UNIQUE INDEX `aliasindex` ON `#__newsworld` (`alias`, `catid`);