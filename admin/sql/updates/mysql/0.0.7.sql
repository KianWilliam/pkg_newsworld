DROP TABLE IF EXISTS `#__newsworld`;

CREATE TABLE `#__newsworld` ( 
    `id` SERIAL NOT NULL, 
    `greeting` VARCHAR(200) NOT NULL, 
    `published` BOOLEAN NOT NULL DEFAULT FALSE, 
    PRIMARY KEY (`id`)
) ENGINE = InnoDB; 

INSERT INTO `#__newsworld` (`greeting`) VALUES
    ('Hello World!'),
    ('Good bye World!');