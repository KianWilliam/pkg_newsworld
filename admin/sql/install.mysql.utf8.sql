DROP TABLE IF EXISTS `#__newsworld`;

CREATE TABLE `#__newsworld` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10)     NOT NULL DEFAULT '0',
	`created`  DATETIME    NOT NULL DEFAULT '0000-00-00 00:00:00',
	`created_by`  INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`checked_out` INT(10) NOT NULL DEFAULT '0',
	`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`title` VARCHAR(25) NOT NULL,
	`description` MEDIUMTEXT NOT NULL,
	`alias`  VARCHAR(40)  NOT NULL DEFAULT '',
    `language`  CHAR(7)  NOT NULL DEFAULT '*',
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`access` tinyint(4) NOT NULL DEFAULT '0',
	`catid`	    int(11)    NOT NULL DEFAULT '0',
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',
	
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

CREATE UNIQUE INDEX `aliasindex` ON `#__newsworld` (`alias`, `catid`);

DROP TABLE IF EXISTS `#__emails`;

CREATE TABLE `#__emails` (
	`id`       INT(11)     NOT NULL AUTO_INCREMENT,
	`user_id` INT(10)     NOT NULL DEFAULT '0',
	`username` VARCHAR(25) NOT NULL,
	`email` VARCHAR(25) NOT NULL DEFAULT '',
    `language`  CHAR(7)  NOT NULL DEFAULT '*',
		`published` tinyint(4) NOT NULL DEFAULT '1',

	`date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',

	
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

