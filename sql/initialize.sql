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


/** Now create a table that maps regions to template regions */
  
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


/** 2012-12-03 Added a field to specify if maps are private or not**/
ALTER TABLE  `maps` ADD  `is_private` TINYINT NOT NULL DEFAULT  '0';

/** 2012-12-03 Willy Douglas added private password column**/
ALTER TABLE  `maps` ADD  `private_password` CHAR(255) NULL DEFAULT  NULL;
  


/** 2012-12-12 Willy Douglas added ignore rows to refernce for saving info for ignored regions **/

/** create ignore map template **/
INSERT INTO  `templates` (
`id` ,
`title` ,
`description` ,
`admin_level` ,
`file` ,
`decimals` ,
`lat` ,
`lon` ,
`zoom`
)
VALUES (
'0',  'Ignore_template',  'ignore',  '0',  'null',  '0',  '0',  '0',  '0'
);

UPDATE  `templates` SET  `id` =  '0' WHERE  `templates`.`title` = 'Ignore_template';

/** Create ignore region **/
INSERT INTO  `template_regions` (
`id` ,
`template_id` ,
`title`
)
VALUES (
'0',  '0',  'ignore_region'
);

UPDATE  `template_regions` SET  `id` =  '0' WHERE  `template_regions`.`title` = 'ignore_region';


/** 2012-12-19 Willy Douglas added new field to map table so that progress of map creation can be tracked **/
ALTER TABLE  `maps` ADD  `map_creation_progress` SMALLINT( 6 ) NOT NULL AFTER  `private_password`;


/** 2012-12-20 Willy Douglas added a new field to mapsheets to track if the sheet is ignored **/
ALTER TABLE  `mapsheets` ADD  `is_ignored` TINYINT NOT NULL DEFAULT  '0';

/**2012-20-21 John Etherton added a new table to track site wide meta data using a key value scheme. This will be where we put things like DB version**/
CREATE TABLE IF NOT EXISTS `metadata` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `k` varchar(255) NOT NULL,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`v`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO  `metadata` (`id` ,`k` ,`v`)
VALUES (NULL ,  'Database Version',  '1.0.016');


/** 2013-01-02 John Etherton added a field to keep track of the orginal KML/KMZ source for the templates**/
ALTER TABLE  `templates` ADD  `kml_file` CHAR( 255 ) NOT NULL AFTER  `file`;
ALTER TABLE  `template_regions` ADD  `original_title` CHAR( 255 ) NOT NULL;
UPDATE `metadata` SET  `v` =  '1.0.017' WHERE  `metadata`.`k` ='Database Version';
ALTER TABLE `metadata` DROP INDEX `uniq_name`;
ALTER TABLE  `metadata` ADD UNIQUE  `uniq_key` (  `k` );


/** Moving the region mapping to the columns table. Dropping the regionmapping table**/
/** First create the new column in the columns table**/
ALTER TABLE  `columns` ADD  `template_region_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL , ADD INDEX (  `template_region_id` );
/** Now move the mappings from regionmapping to columns **/
UPDATE `columns`, `regionmapping` SET `columns`.`template_region_id` = `regionmapping`.`template_region_id` WHERE `columns`.`id` = `regionmapping`.`column_id`;
/** Now drop region mapping **/
DROP TABLE regionmapping;
/** now update the DB version **/
UPDATE `metadata` SET  `v` =  '1.0.018' WHERE  `metadata`.`k` ='Database Version';


/** Dylan creating tables to hold map statistics Jan 25, 2013 **/
CREATE TABLE IF NOT EXISTS `usagestatistics` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `map_id` int(11) UNSIGNED NOT NULL,
  `visits` int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
ALTER TABLE `usagestatistics`ADD CONSTRAINT `statistics_map_id_fk` FOREIGN KEY (`map_id`) REFERENCES `maps` (`id`) ON DELETE CASCADE;
ALTER TABLE `usagestatistics` ADD INDEX  `usagestats_map` (`map_id`);
ALTER TABLE `usagestatistics` ADD INDEX  `usagestats_date` (`date`);
/** now update the DB version **/
UPDATE `metadata` SET  `v` =  '1.0.019' WHERE  `metadata`.`k` ='Database Version';

/** Dylan updating maps table to hold show_empty_name column on Jan 30, 2013 **/
ALTER TABLE  `maps` ADD  `show_empty_name` BOOLEAN NOT NULL DEFAULT TRUE;
/** now update the DB version **/
UPDATE `metadata` SET  `v` =  '1.0.020' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- Added index to maps.title, maps.descrition, and template.title to map searching faster**/
ALTER TABLE  `maps` ADD INDEX  `maps_title_index` (  `title` ( 13 ) );
ALTER TABLE  `maps` ADD INDEX  `maps_description_index` (  `description` ( 20 ) );
ALTER TABLE  `templates` ADD INDEX  `templates_title_index` (  `title` ( 13 ) );
UPDATE `metadata` SET  `v` =  '1.0.021' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- added columns in the templates table to track who added what template and to know if it's an official template or not**/
ALTER TABLE  `templates` ADD  `user_id` INT( 11 ) UNSIGNED NOT NULL , ADD  `is_official` BOOLEAN NOT NULL DEFAULT FALSE , ADD INDEX (  `user_id` ,  `is_official`);
UPDATE  `templates` SET  `user_id` =  '1' WHERE  `templates`.`id` = `templates`.`id`;

UPDATE `templates` SET `is_official` = '1' WHERE `templates`.`id` = `templates`.`id`;
UPDATE `metadata` SET  `v` =  '1.0.022' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- Added a column for is public to templates */
ALTER TABLE  `templates` ADD  `is_private` BOOLEAN NOT NULL DEFAULT FALSE , ADD INDEX (  `is_private` );
UPDATE `metadata` SET  `v` =  '1.0.023' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie --added a column for label_zoom_level in maps **/
ALTER TABLE  `maps` ADD  `label_zoom_level` INT NOT NULL DEFAULT  '1';
UPDATE `metadata` SET  `v` =  '1.0.024' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- 2013-02-01 -- Added an index to the templates table for the description column */
ALTER TABLE  `templates` ADD INDEX  `templates_description_index` (  `description` ( 20 ) );
UPDATE `metadata` SET  `v` =  '1.0.025' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- 2013-02-01 -- Added the max_items column to the roles table to define how many maps a given group can have.*/
ALTER TABLE  `roles` ADD  `max_items` INT NOT NULL DEFAULT  '-1';
UPDATE  `roles` SET  `max_items` =  '5' WHERE  `roles`.`id` =1;
UPDATE `metadata` SET  `v` =  '1.0.026' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie 2013-02-04--added a message_center table **/
CREATE TABLE IF NOT EXISTS `message_center` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `poster_name` char(255) NOT NULL,
  `poster_email` char(255) NOT NULL,
  `message` text NOT NULL,
  `unread` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
UPDATE `metadata` SET  `v` =  '1.0.027' WHERE  `metadata`.`k` ='Database Version';


/** John Etherton -- 2013-02-05 -- Added a table to let users share their table with other users and to let them edit their maps**/
CREATE TABLE IF NOT EXISTS `sharing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `permission` char(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE  `sharing` ADD INDEX  `sharing map_id` (  `map_id` );
ALTER TABLE  `sharing` ADD INDEX  `sharing user_id` (  `user_id` );
ALTER TABLE  `sharing` ADD FOREIGN KEY (  `map_id` ) REFERENCES  `maps` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;
ALTER TABLE  `sharing` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;
UPDATE `metadata` SET  `v` =  '1.0.028' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie 2013-02-06--added a message_center index **/
ALTER TABLE `message_center` ADD INDEX  `Message_map_id` (  `map_id` );
ALTER TABLE  `message_center` CHANGE  `map_id`  `map_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `message_center` ADD FOREIGN KEY (  `map_id` ) REFERENCES  `maps` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;
UPDATE `metadata` SET  `v` =  '1.0.029' WHERE  `metadata`.`k` ='Database Version';





/** Dylan Gillespie 2013-02-11 --added font rows for region and value in maps **/
ALTER TABLE  `maps` ADD  `region_label_font` INT NOT NULL DEFAULT  '12', ADD  `value_label_font` INT NOT NULL DEFAULT  '12';
UPDATE `metadata` SET  `v` =  '1.0.030' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton 2013-02-12 -- Moving map permissions wholly to the sharing table. 
This SQl will populate the sharing table with the necessary permissions for existing owners to be properly recognized**/
INSERT INTO sharing (map_id, user_id, permission)
SELECT id, user_id, 'owner' FROM maps;
ALTER TABLE `maps` DROP `private_password`;
UPDATE `metadata` SET  `v` =  '1.0.031' WHERE  `metadata`.`k` ='Database Version';


/** John Etherton -- 2013-02-12 -- Give users the ability to pick their spam preferences **/
ALTER TABLE  `users` ADD  `email_alerts` BOOLEAN NOT NULL DEFAULT FALSE ,ADD  `email_warnings` BOOLEAN NOT NULL DEFAULT TRUE;
UPDATE `metadata` SET  `v` =  '1.0.032' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- 2013-02-12 -- Make the messaging system more flexible, just send messages to users, not maps**/
ALTER TABLE  `message_center` DROP FOREIGN KEY  `message_center_ibfk_1` ;
ALTER TABLE  `message_center` CHANGE  `map_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `message_center` DROP INDEX  `Message_map_id` , ADD INDEX  `Message_map_id` (  `user_id` );
DELETE FROM `message_center` WHERE 1;
ALTER TABLE  `message_center` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;
ALTER TABLE  `message_center` CHANGE  `date`  `date` DATETIME NOT NULL;
RENAME TABLE  `message_center` TO  `message` ;
UPDATE `metadata` SET  `v` =  '1.0.033' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- 2013-02-14 -- Adding a column to store password reset hashes**/
ALTER TABLE  `users` ADD  `reset_hash` CHAR( 64 ) NULL;
ALTER TABLE  `users` ADD  `reset_expire` DATETIME NULL;
UPDATE `metadata` SET  `v` =  '1.0.034' WHERE  `metadata`.`k` ='Database Version';


/** John Etherton -- 2013-02-15 -- Adding a column to store the slug of a map**/
ALTER TABLE  `maps` ADD  `slug` CHAR( 128 ) NULL AFTER  `description`;
UPDATE maps SET slug = id;
ALTER TABLE  `maps` ADD UNIQUE (`slug`);
UPDATE `metadata` SET  `v` =  '1.0.035' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-02-18 -- Added large_file boolean to maps to give warning on large files **/
ALTER TABLE  `maps` ADD  `large_file` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `file`;
UPDATE `metadata` SET  `v` =  '1.0.036' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-02-20 -- Added default colors to map for style **/
ALTER TABLE  `maps` ADD  `border_color` CHAR( 6 ) NOT NULL DEFAULT  '06D40D' AFTER  `CSS` ;
ALTER TABLE  `maps` ADD  `region_color` CHAR( 6 ) NOT NULL DEFAULT  'AAAAAA' AFTER  `border_color`;
ALTER TABLE  `maps` ADD  `polygon_color` CHAR( 13 ) NOT NULL DEFAULT  'FF0000 FFFFFF' AFTER  `region_color`;
ALTER TABLE  `maps` ADD  `graph_bar_color` CHAR( 6 ) NOT NULL DEFAULT  '223953' AFTER  `polygon_color` ;
ALTER TABLE  `maps` ADD  `graph_select_color` CHAR( 6 ) NOT NULL DEFAULT  'D71818' AFTER  `graph_bar_color`;
UPDATE `metadata` SET  `v` =  '1.0.037' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-02-22 -- Added gradient bool to maps **/
ALTER TABLE  `maps` ADD  `gradient` BOOLEAN NOT NULL DEFAULT FALSE AFTER  `region_color`;
UPDATE `metadata` SET  `v` =  '1.0.038' WHERE  `metadata`.`k` ='Database Version';

/** John Etherton -- 2013-02-28 -- Added tables for tracking OpenID logins use the DB**/
CREATE TABLE oid_nonces (
            server_url VARCHAR(2047) NOT NULL,
            timestamp INTEGER NOT NULL,
            salt CHAR(40) NOT NULL,
            UNIQUE (server_url(255), timestamp, salt)
            ) ENGINE=InnoDB;

CREATE TABLE oid_associations (
            server_url VARCHAR(2047) NOT NULL,
            handle VARCHAR(255) NOT NULL,
            secret BLOB NOT NULL,
            issued INTEGER NOT NULL,
            lifetime INTEGER NOT NULL,
            assoc_type VARCHAR(64) NOT NULL,
            PRIMARY KEY (server_url(255), handle)
            ) ENGINE=InnoDB;
UPDATE `metadata` SET  `v` =  '1.0.039' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-03-01 -- Added table for custom html pages **/
CREATE TABLE IF NOT EXISTS `custompage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `slug` char(130) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE  `custompage` ADD INDEX (  `user_id` );
UPDATE `metadata` SET  `v` =  '1.0.040' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-03-06 -- Added special in table for custom html pages **/
ALTER TABLE  `custompage` ADD  `special` BOOLEAN NOT NULL DEFAULT FALSE;
UPDATE `metadata` SET  `v` =  '1.0.041' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-03-08 -- Added hardcoded main, support, help, and about pages **/
INSERT INTO  `custompage` (
`id` ,
`user_id` ,
`slug` ,
`content` ,
`special`
)
VALUES (
NULL ,  '0',  '__MAIN__',  '<h1>Welcome to Kobo Maps</h1> <p>KoBoMap is the latest KoBo product and is currently in development. The idea of KoBoMap is to facilitate the geospatial presentation of survey data. Survey data are typically available at national or sub-national levels. Few organizations have the capability to produce maps, yet, the ability to represent geographically various indicators can yield important information about the spatial distribution of events and population characteristics. Maps also provide an easy way to share information without having to release raw data. </p><p>KoBoMap is currently being developed using results from a survey conducted in 2010 among 4,501 adult Liberians randomly selected throughout the country. Results can be seen here. </p>',  '1'
), (
NULL ,  '0',  '__HELP__',  '<p><strong>This is the template for the Help page.</strong></p>
<p><strong>Owners will be able to edit this with their own information.</strong></p>',  '1'
), (
NULL ,  '0',  '__ABOUT__',  '<p><strong>This is the template for the About page.</strong></p>
<p><strong>Owners will be able to edit this with their own information.</strong></p>',  '1'
), (
NULL ,  '0',  '__SUPPORT__',  '<p><strong>This is the template for the Support page.</strong></p>
<p><strong>Owners will be able to edit this with their own information.</strong></p>',  '1'
);
UPDATE `metadata` SET  `v` =  '1.0.042' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -- 2013-03-13 -- Added menus table for menu system **/
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` int(11) NOT NULL,
  `text` char(128) NOT NULL,
  `image_url` text NOT NULL,
  `item_url` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `menu` (`menu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
ALTER TABLE  `menu_items` ADD INDEX (`menu`);
ALTER TABLE  `menu_items` ADD FOREIGN KEY (`menu`) REFERENCES  `menus` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;
UPDATE `metadata` SET  `v` =  '1.0.043' WHERE  `metadata`.`k` ='Database Version';


/** John Etherton -- 2013-03-14 -- Added a place for Open ID ids in the user table**/
ALTER TABLE  `users` ADD  `open_id` CHAR( 255 ) NULL DEFAULT NULL AFTER  `password`;
ALTER TABLE  `users` ADD INDEX  `open_id_index` (  `open_id` );
ALTER TABLE  `users` CHANGE  `password`  `password` CHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
UPDATE `metadata` SET  `v` =  '1.0.044' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie --2013-03-22 -- Changing main static page to home since main is also a controller
 also edited the custompage to have a foreign key constraint that deletes pages if the user is deleted **/
UPDATE  `custompage` SET  `slug` =  '__HOME__' WHERE  `custompage`.`id` =1;
INSERT INTO `users` (
`id` ,
`email` ,
`username` ,
`password` ,
`open_id` ,
`logins` ,
`last_login` ,
`first_name` ,
`last_name` ,
`email_alerts` ,
`email_warnings` ,
`reset_hash` ,
`reset_expire`
)
VALUES (
'1',  '',  'ADMIN_PAGE_CREATOR',  'password', NULL ,  '0', NULL ,  'ADMIN',  'For keeping special pages present',  '0',  '1', NULL , NULL
);
UPDATE  `custompage` SET  `user_id` =  '1' WHERE  `custompage`.`id` =1;
UPDATE  `custompage` SET  `user_id` =  '1' WHERE  `custompage`.`id` =2;
UPDATE  `custompage` SET  `user_id` =  '1' WHERE  `custompage`.`id` =3;
UPDATE  `custompage` SET  `user_id` =  '1' WHERE  `custompage`.`id` =4;

ALTER TABLE  `custompage` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED;
ALTER TABLE `custompage` ADD FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
UPDATE `metadata` SET  `v` =  '1.0.045' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie --2013-04-09 -- Changing custompages and submenus for pages to have a menu **/
ALTER TABLE  `custompage` ADD  `my_menu` INT( 11 ) NOT NULL DEFAULT  '0';
INSERT INTO  `menus` (`id` ,`title`) VALUES ('1',  'help');
ALTER TABLE  `menu_items` ADD  `admin_only` BOOLEAN NOT NULL DEFAULT FALSE;
INSERT INTO  `menu_items` (
`id` ,
`menu` ,
`text` ,
`image_url` ,
`item_url` ,
`admin_only`
)
VALUES 
(NULL ,  '1',  'Help Making Maps',  '',  'maphelp',  '0'), 
(NULL ,  '1',  'Help Making Templates',  '',  'templatehelp',  '0'),
(NULL ,  '1',  'Help Making Custom Pages',  '',  'custompagehelp',  '1'), 
(NULL ,  '1',  'Help Making Submenus',  '',  'submenuhelp',  '1');
UPDATE  `custompage` SET  `my_menu` =  '1' WHERE  `custompage`.`id` = 2;
UPDATE `metadata` SET  `v` =  '1.0.046' WHERE  `metadata`.`k` ='Database Version';


ALTER TABLE menu_items DROP INDEX menu_2;
ALTER TABLE  `menu_items` DROP FOREIGN KEY  `menu_items_ibfk_1` ;
ALTER TABLE menu_items DROP INDEX menu;
ALTER TABLE  `menu_items` CHANGE  `menu`  `menu_id` INT( 11 ) NOT NULL;
ALTER TABLE  `menu_items` ADD FOREIGN KEY (  `menu_id` ) REFERENCES  `kobomaps`.`menus` (
	`id`) ON DELETE CASCADE ON UPDATE NO ACTION ;
UPDATE `metadata` SET  `v` =  '1.0.047' WHERE  `metadata`.`k` ='Database Version';
	
/** John Etherton --2013-04-015-- changed the name of the menu field to follow convention
use foreignfieldname_id**/	
ALTER TABLE  `custompage` CHANGE  `my_menu`  `menu_id` INT( 11 ) NOT NULL DEFAULT  '0';
UPDATE `metadata` SET  `v` =  '1.0.048' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie --2013-04-18-- adding help pages to the database **/
INSERT INTO  `custompage` (
`id` ,
`user_id` ,
`slug` ,
`content` ,
`special` ,
`menu_id`
)
VALUES 
(NULL ,  '1',  'maphelp',  '<p><strong>Help on how to create a map.</strong><br /><br />This page will go over how to create a map using Kobomaps.</p><p><br /><strong>Basic Set-Up</strong></p><ol><li><em>Map Title:&nbsp;</em>This is the title for how you will find and use your map, this cannot be more than 156 characters long.</li><li><em>Map Slug</em>:&nbsp;This is the url name that you can type into the address and link right to the map. Be careful though, since it is used in a url, you cannot use characters such as @, %, \", /, etc.</li><li><em>Map Description</em>:&nbsp;This is the description to help you and others know what the information on the map means. You can make this very descriptive and long.</li><li><em>Should this map be hidden from public view?</em>:&nbsp;Checking this box will mean that only you and users you allow will be able to see this map.</li><li><em>Is the data source</em>:&nbsp;Kobomaps can be created with either an Excel Spreadsheet or a GoogleDoc Spreadsheet, just choose which file you are using.</li><li><em>Spreadsheet (.xls, .xlsx):</em>&nbsp;Use this to load the file.</li><li><em>Show advanced options:</em>&nbsp;Clicking on this bar will reveal more advanced options, seen below.</li></ol>
<p style="padding-left: 30px;"><strong>Advanced Options</strong></p><ol><ol><li><em>Show All Labels:</em>&nbsp;If this box is checked, this map will show all the region names, even if there was no data submitted for them. Such as not having data for Colorado, USA, the name Colorado will still appear if this box is checked.</li><li><em>Zoom level to show labels:</em>&nbsp;This number is the zoom level within Googlemaps when the labels and names will start to appear on the map.</li><li><em>Font size of region names:</em>&nbsp;Controls the font size of the names of all the regions.</li><li><em>Font size of data values:</em>&nbsp;Controls the font size of the data labels of all the regions.</li><li><em>Color of region borders:</em>&nbsp;This color is the border lines between regions.</li><li><em>Default color of regions:</em>&nbsp;This is the color of the regions that have not been colored by the data shading.</li><li><em>Make regions have a gradient?:</em>&nbsp;Checking this box will give you the option to set the lower gradient, the default color for the graident is from the specified color into white.</li><li><em>Color of region shading:</em>&nbsp;This is the color that will be more prominent on the higher values of the data for regions that have data. The second color is the gradient end color, default is white. This will be prominent on the lower values of the data.</li><li><em>Color of bars in graphs:</em>&nbsp;This will be the basic color of the bar graphs present on the maps.</li>
<li><em>Color of selected regions in graphs:&nbsp;</em>The bar color for the selected indicator and region, helps the data stand out that you are focused on.</li><li><em>Map CSS:</em>&nbsp;You can enter your own CSS to change the colors and fonts and font size that were not covered by this setup.</li><li><em>Map Style:</em> You can change the colors and visibility of the map, however there will be a page at the end where it is easier to edit this.</li></ol></ol><p><br /><strong>Data Stucture</strong></p><p style="padding-left: 30px;">This page should have most of the explainations required for itself, but double check the selected column and row match what you want, they are looked at very carefully by the program that designs the map.</p><p><strong>Validation</strong></p><p style="padding-left: 30px;">On this page you check and make sure that the program has looked at your spreadsheet correctly, if names or units are too long, the page will warn you as it will make the map over-extend its formatting.</p><p><strong>Geo Set-up</strong></p><p style="padding-left: 30px;">On this page you choose a template to display your information on. Ideally you have already created a template, or you can use a template that is available publically.</p><p><strong>Geo Matching</strong></p>
<p style="padding-left: 30px;">On this page you match all the regions that have been found in the template with the regions that were entered in the spreadsheet. The program will attempt to fill in as many regions as it can find that are simliar.&nbsp;<strong>You cannot use a region more than once.</strong></p><p><strong>Map Style</strong></p><p style="padding-left: 30px;">On this page you can set up the base Googlemap style easier than on the Basic Set-up page.&nbsp;</p><p style="padding-left: 30px;">There are 2 options for each selection, the "label" and "geometry". The Label is the text that pops up on the map that describes the region over which it hovers, while the geometry is the actually land/water of the area, and each of these can have seperate colors.</p><ol><li><em>administrative.province:</em>&nbsp;This option is for state/province border lines.&nbsp;</li><li><em>administrative.locality:</em> This options if for cities and their labels.</li><li><em>poi:</em> These are the points of interest within cities, such as parks, museums, buildings, etc.</li><li><em>road:</em> These are the roads and highways that run throughout countries.</li><li><em>landscape:&nbsp;</em>This is the features of the continents, will cover all other places that are not part of the data for the map. The landscape defaults to a darker grey color.</li><li><em>water:</em>&nbsp;These are the options for the oceans, lakes, and rivers. Having the visibility off and no color selected will default the water to a sky blue color.</li>
</ol><p style="padding-left: 30px;">&nbsp;</p><p><strong>And that should be it to complete your map! When you hit submit on the Map Style page, your map will be created and you will be able to use it.</strong></p><p>&nbsp;</p>',  '1',  '1'),
(NULL ,  '1',  'custompagehelp',  '<p><strong>This is the help page for creating your own custom pages for kobomaps</strong><br /><br /> This page should only be available to administrators for the website and is used to create custom html webpages for the website.<br /><br /> The list of the current pages that can be edited is found on the left side of the page, selecting&nbsp;<em>New Page&nbsp;</em>will allow you to create a brand new page.<br /><br /> Be aware the pages marked with underscores (__HOME__) cannot be deleted as they are needed for the website regardless, but can be edited as you wish. This also applies to the help pages.<br /><br /><br /> <em>Title of Page:&nbsp;</em>This is the slug that you will use to navigate to the page in the URL or submenu items you create later. It needs to be a unique slug that is not used anywhere else on the website. As it is a URL, it cannot contain characters such as @, ", spaces, etc.<br /><br /> <em>Content of Page:&nbsp;</em>This is a tool that easily helps you create webpages created in HTML, but you do not need any knowledge of HTML to use it.&nbsp;</p>
<p>It has the basic tools for&nbsp;<strong>Bold</strong>,<em>&nbsp;Italics</em>,&nbsp;<span style="text-decoration: underline;">Underline</span>, <span class="errors" style="font-size: small;">formatting</span>,&nbsp;<span style="color: #339966;">changing colors</span> and<span style="font-family: "andale mono", times; font-size: large;"> fonts</span>.</p>
<p>It also has some advanced options that are found by clicking the Show/Hide Toolbars button found in the upper right.<br /> These allow for links to other sites, table creation, and even CSS can be found here.</p>
<p><br /> <strong>Be sure to save the page that you are working on before leaving, you can hit either ctrl+s or the Save button.</strong><br /><br /></p>',  '1',  '1'),
(NULL ,  '1',  'templatehelp',  '<p><strong>Help for creating a template</strong></p><p>There is one easy page for creating a template and this is how you do it:</p><ol><li><em>Template Title</em>:&nbsp;This is the title of the template, this is how you will access and use the template.</li><li><em>Template Description</em>:&nbsp;This should be how you explain what the template should be used for and can be as long as you need.</li><li><em>Visibility</em>:&nbsp;Decides if only you or everyone can use this template.</li><li><em>File</em>:&nbsp;This is the file that creates the template, needs to be .kml or .kmz.</li><li><em>Admin Level</em>:&nbsp;What level of admin is required to use the template.</li><li><em>How many decimal places to round to</em>:&nbsp;How accurate the borders on the regions are, the lower the rounding, the faster the template loads, but the less accurate it will be.</li><li><em>By default, what should the center point latitude be</em>:&nbsp;This is the latitude for the template to focus on and first appear at.</li><li><em>By default, what should the center point longitude be:</em>&nbsp;This is the longitude for the template to focus on and first appear at.</li><li><em>By default what should this map zoom to:</em>&nbsp;This is how closs the template should zoom automatically, the small the number, the farther zoomed out the template is.</li>
</ol>',  '1',  '1'),
(NULL ,  '1',  'submenuhelp',  '<p><strong>This is the help page for creating submenus</strong><br /><br />This page should only be available to administrators of the website and is used to create submenus for any custom pages that you have made or any of the three default pages: <em>main,&nbsp;about,</em>&nbsp;and <em>support</em>.</p><p>Upon loading the page, you will see a table that holds the menus and their items.</p><table style="height: 131px; width: 746px; border: 1px solid #000000;" border="0"><thead><tr style="background-color: #d6d7d6;"><td><span style="font-size: small;"><strong>Submenu</strong></span></td><td><span style="font-size: small;"><strong>Items</strong></span></td><td><span style="font-size: small;"><strong>Actions</strong></span></td></tr></thead><tbody><tr align="left" valign="top"><td><span style="font-size: small;">This is the name of the menu that the corresponding items are in.</span></td><td><span style="font-size: small;">This is the representation of how the submenu will appear on the page. Clicking edit will open a pop up window allowing you to change its properties.</span></td><td><span style="font-size: small;">These are the actions that you can do on the menu. <strong>Delete</strong> will delete the menu and all the items it contains. <strong>Edit</strong> will allow you to change the name. And <strong>Add Item</strong> will allow you to put another item in the menu.</span></td></tr></tbody></table><p>&nbsp;</p>
<p>Below the table you will see options to create a new Menu.</p><p>&nbsp;</p><p><strong>Create a new menu.</strong></p><p>The menu just requires a title by which you will identify it. Clicking this save button will create the menu.</p><p>&nbsp;</p><p>The pop up page on clicking <strong>Add Item</strong> or<strong> Edit</strong> an item will show you this:</p><p><em>Title of menu item:</em> This is the text that will be displayed below the item, such as "Create submenus" located above.</p><p><em>Menu URL:&nbsp;</em>This is the name of the page that the menu item will link to when clicked on.</p><p><em>Icon (.jpeg, .png, .bmp):&nbsp;</em>Use this to upload an image file for the display of the menu item.</p><p><em>Only visible by Admins?</em>: This checkbox will say if the menu item will be seen by everyone or only by Admins, selected is for Admins only.</p><p>And clicking save here will save the menu item into the menu that was selected. You can also click delete to get rid of this item in the menu.</p>',  '1',  '1');


ALTER TABLE `templates`ADD CONSTRAINT `template_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
UPDATE `metadata` SET  `v` =  '1.0.049' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie ---- Updating map help pages with images  **/
UPDATE `custompage` SET `content` = '<p><span style="text-decoration: underline;"><strong>Help on how to create a map.</strong></span><br /><br />This page will go over how to create a map using Kobomaps.</p><p><br /><span style="text-decoration: underline;"><strong>Basic Set-Up</strong></span></p><ol><li><strong><em>Map Title:&nbsp;</em></strong>This is the title for how you will find and use your map, this cannot be more than 156 characters long.<strong><img src="media/img/Help/map_title.png" alt="" width="608" height="36" /></strong></li><li><strong><em>Map Slug</em>:</strong>&nbsp;This is the url name that you can type into the address and link right to the map. Be careful though, since it is used in a url, you cannot use characters such as @, %, ", /, etc.<strong><img src="media/img/Help/map_slug_good.png" alt="Slug examples." width="634" height="308" /></strong></li><li><strong><em>Map Description</em>:&nbsp;</strong>This is the description to help you and others know what the information on the map means. You can make this very descriptive and long.<strong><img src="media/img/Help/map_description.png" alt="Description example." /></strong></li><li><strong><em>Should this map be hidden from public view?</em>:&nbsp;</strong>Checking this box will mean that only you and users you allow will be able to see this map.<img src="media/img/Help/private.png" alt="Private checkbox" width="515" height="48" /></li><li><strong><em>Is the data source</em>:&nbsp;</strong>Kobomaps can be created with either an Excel Spreadsheet or a GoogleDoc Spreadsheet, just choose which file you are using.</li><li><strong><em>Spreadsheet (.xls, .xlsx):</em>&nbsp;</strong>Use this to load the file.</li><li><strong><em>Show advanced options:</em>&nbsp;</strong>Clicking on this bar will reveal more advanced options, seen below.</li></ol><p style="padding-left: 30px;"><span style="text-decoration: underline;"><strong>Advanced Options</strong></span></p><ol><ol><li><em><strong>Show All Labels:</strong></em>&nbsp;If this box is checked, this map will show all the region names, even if there was no data submitted for them. Such as not having data for Colorado, USA, the name Colorado will still appear if this box is checked.<img src="media/img/Help/show_region.png" alt="Hidden region titles example." width="990" height="310" /></li><li><strong><em>Zoom level to show labels:</em></strong>&nbsp;This number is the zoom level within Googlemaps when the labels and names will start to appear on the map.<img src="media/img/Help/zoom_level.png" alt="Example of zoom levels" width="809" height="362" /></li><li><strong><em>Font size of region names:</em></strong>&nbsp;Controls the font size of the names of all the regions. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/region_font.png" alt="Example of region font sizes." width="819" height="368" /></li><li><strong><em>Font size of data values:</em></strong>&nbsp;Controls the font size of the data labels of all the regions. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src="media/img/Help/data_font.png" alt="Font size for data example." width="819" height="376" /></li><li><strong><em>Color of region borders:</em></strong>&nbsp;This color is the border lines between regions. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/border.png" alt="Example of default border color." width="786" height="160" /></li><li><strong><em>Default color of regions:</em></strong>&nbsp;This is the color of the regions that have not been colored by the data shading. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/region_color.png" alt="Example of default region color." width="796" height="200" /></li><li><strong><em>Make regions have a gradient?:</em>&nbsp;</strong>Checking this box will give you the option to set the lower gradient, the default color for the graident is from the specified color into white.<img src="media/img/Help/gradient.png" alt="Gradient example" width="692" height="110" /></li><li><strong><em>Color of region shading:</em></strong>&nbsp;This is the color that will be more prominent on the higher values of the data for regions that have data. The second color is the gradient end color, default is white. This will be prominent on the lower values of the data. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/gradient_region.png" alt="Example from red to white gradient." width="451" height="246" /></li><li><strong><em>Color of bars in graphs:</em>&nbsp;</strong>This will be the basic color of the bar graphs present on the maps. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong><em><img src="media/img/Help/bar_color.png" alt="Example of bar color." width="240" height="130" /></em></strong></li><li><strong><em>Color of selected regions in graphs:&nbsp;</em></strong>The bar color for the selected indicator and region, helps the data stand out that you are focused on. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/selected_bar_color.png" alt="Example of selected region bar color." width="338" height="148" /></li><li><strong><em>Map CSS:</em></strong>&nbsp;You can enter your own CSS to change the colors and fonts and font size that were not covered by this setup.</li></ol></ol><p><br /><span style="text-decoration: underline;"><strong>Data Stucture</strong></span></p><p style="padding-left: 30px;">This page should have most of the explainations required for itself, but double check the selected column and row match what you want, they are looked at very carefully by the program that designs the map.</p><p><span style="text-decoration: underline;"><strong>Validation</strong></span></p><p style="padding-left: 30px;">On this page you check and make sure that the program has looked at your spreadsheet correctly, if names or units are too long, the page will warn you as it will make the map over-extend its formatting.</p><p><span style="text-decoration: underline;"><strong>Geo Set-up</strong></span></p><p style="padding-left: 30px;">On this page you choose a template to display your information on. Ideally you have already created a template, or you can use a template that is available publically.</p><p><span style="text-decoration: underline;"><strong>Geo Matching</strong></span></p><p style="padding-left: 30px;">On this page you match all the regions that have been found in the template with the regions that were entered in the spreadsheet. The program will attempt to fill in as many regions as it can find that are simliar.&nbsp;<strong>You cannot use a region more than once.</strong></p><p><span style="text-decoration: underline;"><strong>Map Style</strong></span></p><p style="padding-left: 30px;">On this page you can set up the base Googlemap style easier than on the Basic Set-up page.&nbsp;</p><p style="padding-left: 30px;">There are 2 options for each selection, the "label" and "geometry". The Label is the text that pops up on the map that describes the region over which it hovers, while the geometry is the actually land/water of the area, and each of these can have seperate colors.</p><ol><li><strong><em>administrative.province:</em>&nbsp;</strong>This option is for state/province border lines.&nbsp;<img src="media/img/Help/admin_label.png" alt="Example of administrative.province colors." width="689" height="334" /></li><li><strong><em>administrative.locality:</em></strong> This option is for cities and their labels.<img src="media/img/Help/admin_local.png" alt="Example of city labels and color." width="708" height="172" /></li><li><strong><em>poi:</em></strong> These are the points of interest within cities, such as parks, museums, buildings, etc.<img src="media/img/Help/poi.png" alt="Example for POIs." width="587" height="196" /></li><li><strong><em>road:</em> </strong>These are the roads and highways that run throughout countries.<img src="media/img/Help/road.png" alt="Example of road colors." width="566" height="172" /></li><li><strong><em>landscape:&nbsp;</em></strong>This is the features of the continents, will cover all other places that are not part of the data for the map. The landscape defaults to a darker grey color. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/landscape.png" alt="Example of landscape colors." width="607" height="174" /></li><li><strong><em>water:</em></strong>&nbsp;These are the options for the oceans, lakes, and rivers. Having the visibility off and no color selected will default the water to a sky blue color. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/water.png" alt="Water color examples." width="745" height="178" /></li></ol><p style="padding-left: 30px;">&nbsp;</p><p><strong>And that should be it to complete your map! When you hit submit on the Map Style page, your map will be created and you will be able to use it.</strong></p><p>&nbsp;</p> '
 WHERE `custompage`.`slug` = 'maphelp';
UPDATE `metadata` SET  `v` =  '1.0.050' WHERE  `metadata`.`k` ='Database Version';

/** Dylan Gillespie -----Updating template help page with images **/
UPDATE `custompage` SET `content` = '<p><span style="text-decoration: underline;"><strong>Help for creating a template</strong></span></p><p>This is the help page for creating templates.</p><p><strong>Warning:&nbsp;</strong>If you are having trouble loading kml or kmz files into our template creator, it could be that the files have characters that are only found in the UTF-8 alphabet. To fix this, you can open the file in Google Earth and save it again as another .kmz or .kml file, as Google Earth knows how to save it correctly, or open the .kml and save it as UTF-8 encoding.</p><p>&nbsp;</p><p><em><strong>All Templates Page:</strong></em></p><p>This page contains two sections: Official Templates, and your personal templates along with other public templates</p><p><img src="media/img/Help/templateTable.png" alt="How the All Templates page should look." width="558" height="488" />.</p><p>&nbsp;</p><p><strong><em>My Templates:</em></strong></p><p>This page should only show you the templates you have made yourself.</p><p>&nbsp;</p><p><strong><em>Create</em> <em>Templates</em>:</strong></p><p>There is one easy page for creating a template and this is how you do it, using the Liberia.kmz that was used for Kobomaps official template for Liberia as an example.</p><ol><li><strong><em>Template Title</em>:</strong>&nbsp;This is the title of the template, this is how you will find and use the template. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src="media/img/Help/templateTitle.png" alt="Example of a title for a template." width="497" height="44" /></li><li><strong><em>Template Description</em>:</strong>&nbsp;This should be how you explain what the template should be used for and can be as long as you need. <img src="media/img/Help/templateDescription.png" alt="Example of a template description." width="688" height="143" /></li><li><strong><em>Visibility</em>:</strong>&nbsp;Decides if only you or everyone can use this template. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src="media/img/Help/templateVisible.png" alt="Showing private or public for visibility." width="270" height="54" /></li><li><strong><em>File</em>:&nbsp;</strong>This is the file that creates the template, needs to be .kml or .kmz. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src="media/img/Help/templateFile.png" alt="Example of Liberia.kmz." width="537" height="54" />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</li><li><strong><em>Admin Level</em>:</strong>&nbsp;What level of administrative districts are contained within the template. The templates on Kobomaps all contain level 1 administrative regions. These are states in the United States, provinces and terroritories in Canada, etc. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/templateAdmin.png" alt="Example of administrative level 1 areas in Liberia." width="278" height="238" />&nbsp;These are the adminstrative level 1 areas for Liberia. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</li><li><strong><em>How many decimal places to round to</em>:</strong>&nbsp;How accurate the borders on the regions are, the lower the rounding, the faster the template loads, but the less accurate it will be. For example:&nbsp;<img src="media/img/Help/templateRounding.png" alt="Liberia with 0 and 2 places rounding." width="559" height="508" />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Rounding to 0 does not give very accurate results on a close scale, the higher the decimal, the more accurate it is. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</li><li><strong><em>By default, what should the center point latitude be</em>:&nbsp;</strong>This is the latitude for the template to focus on and first appear at. You do not have to be very accurate, this is only an estimate as on the next page you will be able to move the map to the exact coordinates you would like.</li><li><strong><em>By default, what should the center point longitude be:</em></strong>&nbsp;This is the longitude for the template to focus on and first appear at.&nbsp;You do not have to be very accurate, this is only an estimate as on the next page you will be able to move the map to the exact coordinates you would like.</li><li><strong><em>By default what should this map zoom to:</em></strong>&nbsp;This is how closs the template should zoom automatically, the small the number, the farther zoomed out the template is.&nbsp;You do not have to be very accurate, this is only an estimate as on the next page you will be able to move the map to the exact zoom you would like. &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src="media/img/Help/templateZoom.png" alt="Example of zoom levels of the template." width="540" height="378" /></li></ol><p>Clicking Add will take you to this page where you can edit anything while moving the map into the position you want it, along with the zoom. Also check that all the regions are there that you want.&nbsp;<img src="media/img/Help/templateFull.png" alt="Example of template edit page." width="644" height="720" /></p><p>&nbsp;</p><p>And those are templates for Kobomaps!</p>'
 WHERE `custompage`.`slug` = 'templatehelp';
UPDATE `custompage` SET `content` = '<h1>Welcome to Kobo Maps</h1><p>KoBoMap is the latest KoBo product and is currently in development. The idea of KoBoMap is to facilitate the geospatial presentation of survey data. Survey data are typically available at national or sub-national levels. Few organizations have the capability to produce maps, yet, the ability to represent geographically various indicators can yield important information about the spatial distribution of events and population characteristics. Maps also provide an easy way to share information without having to release raw data.</p><p>&nbsp;<img src="media/img/Help/mainPage1.png" alt="Example of the site" width="778" height="381" /></p><p><strong>It is easy to get started! All you need to do to start making maps to help present data is the following:</strong></p><p><strong>1. Make an account:&nbsp;</strong>Making an account will let you save maps and templates and access the rest of the site. You can do that <a href="signup">here</a>. You can create a map using either Microsoft Excel spreadsheets, or if you have a Google account, you can use your Google account to log in to the site. To do that go <a href="login">here</a> and select "OpenID Login" and choose Google.</p><p><strong>2. Create a template:&nbsp;</strong>To create a map, you first need to make sure you have a template to use. Kobomaps has a large list of templates already loaded, consisting of the countries of the world that you can use instead of making your own. After having an account you can make templates <a href="templates/edit">here</a>. If you need any help, there is a <a href="templatehelp">help page</a> available.</p><p><strong>3. Create a map:&nbsp;</strong>Starting <a href="mymaps/add1">here</a>, you can go through the steps to create a geospatial presentation of any data that you wish. If you need any help, there is a <a href="maphelp">help page</a> available.</p><p><img src="media/img/Help/mainPage2.png" alt="Example of tables in Kobomaps." width="780" height="530" /></p>'
 WHERE `custompage`.`slug` = '__HOME__';
UPDATE `metadata` SET `v` = '1.0.051' WHERE `metadata`.`k` = 'Database Version';