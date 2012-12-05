/*************************************************/
/**Setup the Authentication system for Kohana**/
/*************************************************/

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES(1, 'login', 'Login privileges, granted after account confirmation');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES(2, 'admin', 'Administrative user, has access to everything.');

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL,
  `logins` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_login` int(10) UNSIGNED,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) UNSIGNED NOT NULL,
  `expires` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;


/****Add last and first names to the users****/

ALTER TABLE  `users` ADD  `first_name` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD  `last_name` VARCHAR( 255 ) NULL DEFAULT NULL;

/**Add the maps table**/
CREATE TABLE IF NOT EXISTS `maps` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(254) NOT NULL,
  `description` LONGTEXT NOT NULL DEFAULT '',
  `user_id` int(11) UNSIGNED NOT NULL,
  `file` char(254) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `maps`
  ADD CONSTRAINT `maps_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;


/**Add the templates table, templates for maps that is**/

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(254) NOT NULL,
  `description` LONGTEXT NOT NULL DEFAULT '',
  `admin_level` int(11) UNSIGNED NOT NULL,
  `file` char(254) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/**Add the level of rounding**/
ALTER TABLE  `templates` ADD  `decimals` INT( 11 ) NOT NULL;
ALTER TABLE  `templates` ADD  `lat` double NOT NULL;
ALTER TABLE  `templates` ADD  `lon` double NOT NULL;
ALTER TABLE  `templates` ADD  `zoom` INT( 11 ) NOT NULL;


CREATE TABLE IF NOT EXISTS `template_regions` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` int(11) UNSIGNED NOT NULL,
  `title` char(254) NOT NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `template_regions` ADD CONSTRAINT `tmpl_regions_tmpl_fk` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE;


/**Add the table for storing different sheets**/
CREATE TABLE IF NOT EXISTS `mapsheets` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `map_id` int(11) UNSIGNED NOT NULL,
  `name` char(254) NOT NULL,
  `position` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `mapsheets`
  ADD CONSTRAINT `mapsheets_maps_fk` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE;
  
/**Add the table for storing different coloumns**/
CREATE TABLE IF NOT EXISTS `columns` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mapsheet_id` int(11) UNSIGNED NOT NULL,
  `name` char(254) NOT NULL,
  `type` char(254) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `columns`
  ADD CONSTRAINT `columns_mapsheets_fk` FOREIGN KEY (`mapsheet_id`) REFERENCES `mapsheets` (`id`) ON DELETE CASCADE;
ALTER TABLE  `columns` ADD INDEX  `column_name` (  `name` );
ALTER TABLE  `columns` ADD INDEX  `column_sheet` (  `mapsheet_id` );

/**Add the table for storing different rows. I had to call it rowss because rows is a reserved word in mysql**/
CREATE TABLE IF NOT EXISTS `rowss` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mapsheet_id` int(11) UNSIGNED NOT NULL,
  `name` char(254) NOT NULL,
  `type` char(254) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `rowss`
  ADD CONSTRAINT `rowss_mapsheets_fk` FOREIGN KEY (`mapsheet_id`) REFERENCES `mapsheets` (`id`) ON DELETE CASCADE;
ALTER TABLE  `rowss` ADD INDEX  `row_name` (  `name` );
ALTER TABLE  `rowss` ADD INDEX  `row_sheet` (  `mapsheet_id` );


/**2012-11-15**/
/**add lat, lon center point, and default zoom levels, CSS, and google maps style to maps db*/
ALTER TABLE  `maps` ADD  `lat` DOUBLE NOT NULL ,
ADD  `lon` DOUBLE NOT NULL ,
ADD  `zoom` INT NOT NULL ,
ADD  `CSS` LONGTEXT NULL ,
ADD  `map_style` LONGTEXT NULL;


/** Now create a table that maps regions to template regions
  
/**Add the table for storing different rows. I had to call it rowss because rows is a reserved word in mysql**/
CREATE TABLE IF NOT EXISTS `regionmapping` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `column_id` int(11) UNSIGNED NOT NULL,
  `template_region_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `regionmapping`ADD CONSTRAINT `regionmapping_column_fk` FOREIGN KEY (`column_id`) REFERENCES `columns` (`id`) ON DELETE CASCADE;
ALTER TABLE `regionmapping`ADD CONSTRAINT `regionmapping_template_fk` FOREIGN KEY (`template_region_id`) REFERENCES `template_regions` (`id`) ON DELETE CASCADE;
ALTER TABLE  `regionmapping` ADD INDEX  `mapping_column` (  `column_id` );
ALTER TABLE  `regionmapping` ADD INDEX  `mapping_region` (  `template_region_id` );

/** a field that sets the template for a map*/
ALTER TABLE  `maps` ADD  `template_id` INT( 11 ) UNSIGNED NOT NULL AFTER  `user_id`;
ALTER TABLE  `maps` ADD  `json_file` char(254) NOT NULL AFTER  `user_id`;

  