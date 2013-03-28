# KoBo Maps Install instructions

## Files
* /application/logs needs to be writeable for the web server
* /application/cache needs to be writeable for the web server
* /uploads needs to be writeable for the web server
* /system/classes/Kohana/Cookie.php needs to have a unique salt value on line 16
* Copy /application/bootstrap.php.template as /application/bootstrap.php and edit the base_url value on line 93
* Copy /example.htaccess as /.htaccess and change the RewriteBase value on line 5
* Copy /application/config/database.php.template as /application/config/database.php and update the database settings inside
