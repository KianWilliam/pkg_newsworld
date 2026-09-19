CREATE TABLE IF NOT EXISTS `#__newsworld` (
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
	`ordering` int(11) NOT NULL default '0',  
    `version` int(10) NOT NULL DEFAULT '0',
	`catid`	    int(11)    NOT NULL DEFAULT '0',
	`params`   VARCHAR(1024) NOT NULL DEFAULT '',	
	PRIMARY KEY (`id`)
) ENGINE =MyISAM DEFAULT CHARSET =utf8;
