CREATE TABLE `#__saola` (
  `id` integer NOT NULL auto_increment,
  `file_name` char(32) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `secret_key` char(32) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;