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
ALTER TABLE `templates`ADD CONSTRAINT `template_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
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
  UNIQUE KEY `slug_id` (`slug_id`)
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
);
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


